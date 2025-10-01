<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Stylist;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle($stylistId)
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('stylist_id', $stylistId)
            ->first();

        if ($favorite) {
            // すでに登録されていれば削除
            $favorite->delete();
            $status = 'removed';
        } else {
            // なければ追加
            Favorite::create([
                'user_id' => Auth::id(),
                'stylist_id' => $stylistId,
            ]);
            $status = 'added';
        }

        // 現在の気になる数を返す
        $count = Favorite::where('stylist_id', $stylistId)->count();

        return response()->json([
            'status' => $status,
            'count' => $count,
        ]);
    }

    public function index()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->with('stylist.user') // スタイリスト情報も取得
            ->get()
            ->pluck('stylist'); // stylist のみ取り出す

        return view('favorites.index', compact('favorites'));
    }
}
