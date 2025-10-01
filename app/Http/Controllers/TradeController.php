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
            ->with(['user', 'photos'])
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
        'user_id' => $stylist->user_id,
        'type'    => 'trade_request',
        'data'    => [
            'message' => (auth()->user()->nickname ?? auth()->user()->name) . ' さんが取引申請しました。',
            'url'     => route('trades.show', $trade->id), // ← URL追加
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
        'message' => ($trade->stylist->user->nickname ?? 'スタイリスト') . ' さんが取引を承認しました！',
        'url'     => route('trades.show', $trade->id),
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
            'html'    => view('transactions.partials.message_section', compact('trade','messages'))->render()
                  . view('transactions.partials.message_form', compact('trade'))->render()
    ], 200, [], JSON_UNESCAPED_UNICODE); // ← JSONで返す
    }

    public function sendMessage(Request $request, $id)
    {
    $request->validate([
        'message'    => 'nullable|string|max:1000', // テキストは任意
        'photos.*'   => 'nullable|image|max:5120',  // 1ファイル 5MB 上限
    ]);

    $trade = Trade::findOrFail($id);

    // 参加者チェック（取引に関わっている人のみ送信可能）
    if (!in_array(auth()->id(), [$trade->user_id, $trade->stylist->user_id])) {
        abort(403, 'この取引に参加していません');
    }

        // ファイル保存処理
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('trade_messages', 'public');
        }

    // 保存
    $message = TradeMessage::create([
        'trade_id' => $trade->id,
        'user_id'  => auth()->id(),
        'message'  => $request->message ?? '',
    ]);

        // 複数画像保存
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('trade_messages', 'public');
                $photo = $message->photos()->create([
                    'photo_path' => $path,
                ]);
                $photos[] = asset('storage/' . $photo->photo_path);
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => $message->message,
            'user'    => $message->user->nickname ?? $message->user->name,
            'time'    => $message->created_at->diffForHumans(),
            'user_id' => $message->user_id,
            'photos'  => $photos, // 複数URLを返す
        ]);

        $messages = TradeMessage::where('trade_id', $trade->id)
        ->with('photos', 'user')
        ->orderBy('created_at', 'asc') // 古い順
        ->get();
    }

    // スタイリストが承認して完全終了
    public function confirmComplete($id)
    {
    $trade = Trade::findOrFail($id);

    if ($trade->stylist->user_id !== auth()->id()) {
        abort(403, '権限がありません');
    }

    if (!$trade->completed_by_user) {
        return back()->with('error', 'ユーザーから終了申請がまだ出されていません。');
    }

    $trade->completed_by_stylist = true;
    $trade->status = 'completed'; // 完全終了ステータス
    $trade->completed_at = now();   // 日時を保存
    $trade->save();

    // ✅ ユーザーに通知を送る
    Notification::create([
        'user_id' => $trade->user_id, // 通知を受け取るのはユーザー
        'type'    => 'trade_completed',
        'data'    => [
        'message' => ($trade->stylist->user->nickname ?? 'スタイリスト') . ' さんとの取引が終了しました。',
        'url'     => route('trades.show', $trade->id),
    ],
        'is_read' => false,
    ]);

    return back()->with('success', '取引が終了しました！');
    }

    public function requestComplete($id)
    {
    $trade = Trade::findOrFail($id);

    // ユーザー本人だけが終了申請できる
    if ($trade->user_id !== auth()->id()) {
        abort(403, '権限がありません');
    }

    // すでに終了申請していたら弾く
    if ($trade->completed_by_user) {
        return back()->with('error', 'すでに取引終了を申請済みです');
    }

    // フラグを更新
    $trade->completed_by_user = true;
    $trade->save();

    // 通知をスタイリストに送る
    Notification::create([
        'user_id' => $trade->stylist->user_id, // スタイリストに通知
        'type'    => 'trade_complete_request',
        'data'    => [
            'from_user' => $trade->user->nickname ?? $trade->user->name,
            'message'   => ($trade->user->nickname ?? 'ユーザー') . ' さんが取引終了の申請をしました。',
            'trade_id'  => $trade->id,
            'url'       => route('trades.show', $trade->id),
        ],
        'is_read' => false,
    ]);

    return back()->with('success', '取引終了を申請しました');
    }

    public function index()
    {
    $userId = auth()->id();

    $ongoingTrades = \App\Models\Trade::where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereHas('stylist', function($q) use ($userId) {
                  $q->where('user_id', $userId);
              });
        })
        ->whereIn('status', ['pending', 'approved']) // 進行中
        ->with(['stylist.user', 'user'])
        ->orderBy('created_at', 'desc')
        ->get();

    $completedTrades = \App\Models\Trade::where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereHas('stylist', function($q) use ($userId) {
                  $q->where('user_id', $userId);
              });
        })
        ->where('status', 'completed') // 完了
        ->with(['stylist.user', 'user'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('trades.index', compact('ongoingTrades', 'completedTrades'));
    }


}
