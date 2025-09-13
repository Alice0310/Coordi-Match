<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    // プロフィール編集画面
    public function edit()
    {
        $user = Auth::user();
        // view('profile.edit') → view('user.profile.edit')
        return view('user.profile.edit', compact('user'));
    }

    // 更新処理
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nickname' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|max:2048', // name属性に合わせる
        ]);

        $user->nickname = $request->nickname;
        $user->description = $request->description;

        // プロフィール画像のアップロード処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image_path = $path;
        }

        $user->save();

        return redirect()->route('user.profile.edit')->with('success', 'プロフィールを更新しました！');
    }
}
