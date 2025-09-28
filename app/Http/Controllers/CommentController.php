<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stylist;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $stylistId)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        $stylist = Stylist::findOrFail($stylistId);

        $comment = $stylist->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->comment,
        ]);

        // 🔔 通知を保存
        Notification::create([
            'user_id' => $stylist->user_id,
            'type'    => 'comment',
            'data'    => [
                'from_user'  => auth()->user()->name,
                'comment_id' => $comment->id,
                'message'    => auth()->user()->name . ' さんがコメントしました。',
                'stylist_id' => $stylist->id,
            ],
            'is_read' => false,
        ]);

        return back()->with('success', 'コメントを送信しました！');
    }

    // コメント削除
    public function destroy($id)
    {
    $comment = Comment::findOrFail($id);

    // コメントが紐づくスタイリストを取得
    $stylist = $comment->stylist;

    // ログインユーザーがそのスタイリスト本人か確認
    if ($stylist->user_id !== Auth::id()) {
        abort(403, 'このコメントを削除する権限がありません。');
    }

    $comment->delete();

    return back()->with('success', 'コメントを削除しました。');
    }
}
