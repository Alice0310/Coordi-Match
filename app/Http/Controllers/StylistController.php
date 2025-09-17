<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Stylist;

class StylistController extends Controller
{
    public function list()
    {
        $stylists = Stylist::where('status', 'published')->get();
        return view('stylist.list', compact('stylists'));
    }

    public function detail($id)
    {
        $stylist = Stylist::findOrFail($id);
        return view('stylist.detail', compact('stylist'));
    }

    public function become()
    {
        $stylist = Stylist::where('user_id', auth()->id())->first(); // 既存があれば取得
        return view('stylist.become', compact('stylist'));
    }

    public function create()
    {
        return view('stylist.become');
    }

    public function storeOrUpdate(Request $request)
    {
    $data = $request->validate([
        'overview'    => 'required|string|max:40',
        'appeal'      => 'nullable|string|max:1000',
        'twitter'     => 'nullable|url',
        'instagram'   => 'nullable|url',
        'price'       => 'nullable|integer',
        'photos.*'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'genres'      => 'nullable',
        'photo_order' => 'nullable|string',
    ]);

    $newPhotos = []; // 今回新規でアップロードされたファイルの保存先
    $finalPhotos = []; // 最終的にDBに入れる配列

    // 1. 新規アップロードされた写真を保存
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('stylists/photos', 'public');
            $newPhotos[] = $path;
        }
    }

    // 2. photo_order に基づいて順番を決定
    if ($request->filled('photo_order')) {
        $ordered = json_decode($request->photo_order, true);

        foreach ($ordered as $item) {
            if ($item && str_starts_with($item, 'stylists/photos/')) {
                // 既存のファイルパス
                $finalPhotos[] = $item;
            } else {
                // null や新規ファイルの枠 → newPhotosから先頭を取り出して割り当て
                if (!empty($newPhotos)) {
                    $finalPhotos[] = array_shift($newPhotos);
                }
            }
        }

        // まだ使われていない新規写真が残っていれば最後に追加
        foreach ($newPhotos as $leftover) {
            $finalPhotos[] = $leftover;
        }
    } else {
        // 並び順指定がなければ新規写真だけ
        $finalPhotos = $newPhotos;
    }

    $data['photos'] = $finalPhotos;

    // 3. ジャンルを配列として保存
    if ($request->filled('genres')) {
        $data['genres'] = is_string($request->genres) ? json_decode($request->genres, true) : $request->genres;
    }

    $data['status'] = 'published';

    // 4. 既存レコードがあれば更新、なければ作成
    Stylist::updateOrCreate(
        ['user_id' => auth()->id()],
        $data
    );

    return redirect()->route('become.stylist')->with('success', 'スタイリスト情報を保存しました！');
}


    public function edit(Stylist $stylist)
    {
        return view('stylists.edit', compact('stylist'));
    }

    public function update(Request $request, Stylist $stylist)
    {
        $data = $request->validate([
            'overview'   => 'required|string|max:40',
            'appeal'     => 'nullable|string|max:1000',
            'twitter'    => 'nullable|url',
            'instagram'  => 'nullable|url',
            'price'      => 'nullable|integer',
            'photos.*'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'genres'     => 'nullable',
            'photo_order'=> 'nullable|string',
        ]);

        // 既存の写真がある場合は最初に取得
        $photos = $stylist->photos ?? [];

        // 新規アップロードがあれば追加
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('stylists/photos', 'public');
                $photos[] = $path;
            }
        }

        // photo_order がある場合、順番を整理
        if ($request->filled('photo_order')) {
            $ordered = json_decode($request->photo_order, true);
            $finalPhotos = [];

            foreach ($ordered as $item) {
                if (in_array($item, $photos)) {
                    $finalPhotos[] = $item;
                }
            }

            $photos = $finalPhotos;
        }

        $data['photos'] = $photos;

        // genres を JSON 配列で保存
        if ($request->filled('genres')) {
            $data['genres'] = is_string($request->genres) ? json_decode($request->genres, true) : $request->genres;
        }

        $data['status'] = $request->has('draft') ? 'draft' : 'published';

        $stylist->update($data);

        return redirect()->route('stylists.index')->with('success', 'スタイリスト情報を更新しました！');
    }

    public function destroy(Stylist $stylist)
    {
        $stylist->delete();
        return redirect()->route('stylists.index')->with('success', 'スタイリストを削除しました');
    }
}
