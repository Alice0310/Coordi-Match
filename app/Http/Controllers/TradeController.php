<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stylist;
use App\Models\Notification;
use App\Models\Trade;
use App\Models\TradeMessage;

class TradeController extends Controller
{
    public function show($id)
    {
        $trade = Trade::with(['stylist.user', 'user'])->findOrFail($id);

        $messages = TradeMessage::where('trade_id', $trade->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    
        return view('transactions.transaction', compact('trade', 'messages'));
    }

    public function request(Request $request, $id)
    {
    $stylist = Stylist::findOrFail($id);

    // 1. Trade を作成
    $trade = Trade::create([
        'stylist_id' => $stylist->id,
        'user_id'    => auth()->id(),
        'status'     => 'pending',
    ]);

    // 2. 通知を保存
    Notification::create([
        'user_id' => $stylist->user_id, // 通知を受け取るのはスタイリスト本人
        'type'    => 'trade_request',
        'data'    => [
            'from_user' => auth()->user()->nickname ?? auth()->user()->name,
            'message'   => (auth()->user()->nickname ?? auth()->user()->name) . ' さんが取引申請しました。',
            'stylist_id'=> $stylist->id,
            'trade_id'  => $trade->id, // ← Trade 作成後なのでOK
        ],
        'is_read' => false,
    ]);

    return response()->json([
        'success' => true,
        'message' => $stylist->overview . ' さんに申請しました！'
    ]);
    }

    public function approve($id)
    {
    $trade = Trade::findOrFail($id);

    // スタイリスト本人だけが承認可能
    if ($trade->stylist->user_id !== auth()->id()) {
        abort(403, '権限がありません');
    }

    // ステータスを承認済みに変更
    $trade->status = 'approved';
    $trade->save();

    // 通知を申請者に送る
    Notification::create([
        'user_id' => $trade->user_id, // 申請した人に通知
        'type'    => 'trade_approved',
        'data'    => [
            'from_user' => $trade->stylist->user->nickname ?? $trade->stylist->user->name,
            'message' => ($trade->stylist->user->nickname ?? 'スタイリスト') . ' さんが取引を承認しました！',
            'trade_id'=> $trade->id,
        ],
        'is_read' => false,
    ]);

        // メッセージ一覧を取得して部分ビューを返す
        $messages = \App\Models\TradeMessage::where('trade_id', $trade->id)
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json([
            'success' => true,
            'message' => '取引が開始されました！',
            'html'    => view('transactions.partials.message_section', compact('trade', 'messages'))->render()
    ]);
    }

    public function sendMessage(Request $request, $id)
    {
    $request->validate([
        'message' => 'required|string|max:1000',
    ]);

    $trade = Trade::findOrFail($id);

    // 参加者チェック（取引の user_id または stylist の user_id であること）
    if (!in_array(auth()->id(), [$trade->user_id, $trade->stylist->user_id])) {
        abort(403, 'この取引に参加していません');
    }

    $message = TradeMessage::create([
        'trade_id' => $trade->id,
        'user_id'  => auth()->id(),
        'message'  => $request->message,
    ]);

    return response()->json([
        'success' => true,
        'message' => $message->message,
        'user'    => $message->user->nickname ?? $message->user->name,
        'time'    => $message->created_at->diffForHumans(),
        'user_id' => $message->user_id,
    ]);
    }


}
