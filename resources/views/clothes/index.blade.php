@extends('layouts.header')

@section('title', '持ち服一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/clothes.css') }}">
@endsection

@section('content')
<div class="clothes-container">
    <h2>持ち服一覧（最大20枚）</h2>

    <!-- 服の登録フォーム -->
    <form action="{{ route('clothes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="photo-preview" class="image-upload-wrapper">
            <!-- プレビューの枠 -->
        </div>
        <!-- + ボタン -->
        <div class="add-photo">+</div>
        <input type="file" id="photos" name="photos[]" accept="image/*" multiple style="display:none;">
        <button type="submit">登録</button>
    </form>

    <!-- エラーメッセージ -->
    @error('photos.*')
      <div class="error">{{ $message }}</div>
    @enderror

    <!-- 保存済みの服一覧 -->
    <div class="clothes-list">
        @foreach($clothes as $cloth)
            <div class="cloth-item">
                <img src="{{ asset('storage/'.$cloth->photo_path) }}" alt="服" width="120">
            </div>
        @endforeach
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(function () {
    const $inputPhotos = $('#photos');
    const $previewContainer = $('#photo-preview');
    const maxPhotos = 20;

    // 「+」クリックでファイル選択
    $('.add-photo').on('click', function() {
        $inputPhotos.click();
    });

    // ファイル選択時
    $inputPhotos.on('change', function () {
        const files = Array.from(this.files);

        // 既存数チェック
        const existingCount = $previewContainer.find('.preview-item').length;
        const remaining = maxPhotos - existingCount;
        if (remaining <= 0) return;

        files.slice(0, remaining).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const $div = $('<div>').addClass('preview-item');
                const $img = $('<img>').attr('src', e.target.result);
                const $remove = $('<span>').addClass('remove-btn').text('×');

                $div.append($img).append($remove);
                $previewContainer.append($div);
            };
            reader.readAsDataURL(file);
        });
    });

    // 削除ボタン
    $previewContainer.on('click', '.remove-btn', function() {
        $(this).closest('.preview-item').remove();
    });
});
</script>
@endsection