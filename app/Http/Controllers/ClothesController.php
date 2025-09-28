<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClothesController extends Controller
{
    public function index()
    {
        $clothes = auth()->user()->clothes()->where('is_active', true)->get();
        return view('clothes.index', compact('clothes'));
    }

    public function store(Request $request)
    {
        $currentCount = auth()->user()->clothes()->count();

        if ($currentCount >= 20) {
            return back()->withErrors(['photos' => '登録できる服は最大20枚までです。']);
        }

        // バリデーション
        $request->validate([
            'photos.*' => 'required|image|max:5120', // 5MBまで
        ]);

        // 保存処理
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                if ($currentCount >= 20) break;

                $path = $file->store('clothes', 'public');
                auth()->user()->clothes()->create([
                    'photo_path' => $path,
                    'is_active'  => true,
                ]);

                $currentCount++;
            }
        }

        return back()->with('success', '服を登録しました！');
    }

    public function showUserClothes($id, $tradeId = null)
    {
    $user = \App\Models\User::findOrFail($id);

    // 取引がある前提なら trade 経由でチェック
    // 例: /transactions/{tradeId}/user/{id}/clothes の形にするとより安全
    if ($tradeId) {
        $trade = \App\Models\Trade::findOrFail($tradeId);

        // ログインユーザーがスタイリスト本人じゃなければ403
        if ($trade->stylist->user_id !== auth()->id()) {
            abort(403, '閲覧権限がありません');
        }
    }

    $clothes = $user->clothes()->where('is_active', true)->get();
    return view('clothes.user_index', compact('user', 'clothes'));
    }
}