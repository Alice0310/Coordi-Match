@extends('layouts.header')

@section('title', 'スタイリスト登録')

@section('css')
<link rel="stylesheet" href="{{ asset('css/become_stylist.css') }}">
@endsection

@section('content')
<div class="stylist-form">
    <h2>スタイリスト登録</h2>

    <form action="{{ route('stylists.store_or_update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 写真選択 -->
        <div class="form-group">
            <label>写真 (最大8枚)</label>
            <div id="photo-preview" class="photo-preview">
                <label for="photos" class="add-photo">＋</label>

                {{-- 既存写真 --}}
                @if(isset($stylist) && $stylist->photos)
                    @foreach($stylist->photos as $photo)
                        <div class="preview-item">
                            <img src="{{ asset('storage/' . $photo) }}" data-filename="{{ $photo }}">
                            <span class="remove-btn">×</span>
                        </div>
                    @endforeach
                @endif
            </div>
            <input type="file" id="photos" name="photos[]" multiple accept="image/*" style="display:none;">
            <input type="hidden" name="photo_order" id="photo-order-input">
        </div>

        <!-- 概要 -->
        <div class="form-group">
            <label for="overview">スタイリスト概要</label>
            <textarea id="overview" name="overview" maxlength="40" rows="2">{{ old('overview', $stylist->overview ?? '') }}</textarea>
            <div class="char-count" id="overview-count">0/40</div>
        </div>

        <!-- タグ -->
        <div class="form-group">
            <div class="input-wrapper">
                <label for="tags">得意ジャンル</label>
                <input type="text" id="tag-input" placeholder="タグを入力">
                <span id="tag-count">0/15</span>
                <div class="tags" id="tags"></div>
                <input type="hidden" name="genres" id="genres-input" value="{{ isset($stylist) ? json_encode($stylist->genres) : '' }}">
            </div>
        </div>

        <!-- アピール文 -->
        <div class="form-group">
            <label for="appeal">アピール文</label>
            <textarea id="appeal" name="appeal" maxlength="1000" rows="5">{{ old('appeal', $stylist->appeal ?? '') }}</textarea>
            <div class="char-count" id="appeal-count">0/1000</div>
        </div>

        <!-- SNS -->
        <div class="form-group">
            <label for="twitter">Twitter</label>
            <input type="url" id="twitter" name="twitter" value="{{ old('twitter', $stylist->twitter ?? '') }}">
        </div>
        <div class="form-group">
            <label for="instagram">Instagram</label>
            <input type="url" id="instagram" name="instagram" value="{{ old('instagram', $stylist->instagram ?? '') }}">
        </div>

        <!-- 価格 -->
        <div class="form-group">
            <label for="price">価格</label>
            <input type="number" id="price" name="price" value="{{ old('price', $stylist->price ?? '') }}">
        </div>

        <!-- 送信ボタン -->
        <div class="form-actions">
            <button type="submit" class="btn-primary">スタイリストになる</button>
            <button type="button" class="btn-secondary">下書き保存</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(function () {
    const $inputPhotos = $('#photos');
    const $previewContainer = $('#photo-preview');
    const maxPhotos = 8;

    // Sortable
    new Sortable($previewContainer[0], {animation: 150, ghostClass: 'sortable-ghost'});

    // イベント委譲で削除
    $previewContainer.on('click', '.remove-btn', function() {
    $(this).closest('.preview-item').remove();
    updateAddPhotoVisibility();
    adjustPreviewHeight();
    });

    // プレビュー追加
    $inputPhotos.on('change', function () {
    const files = Array.from(this.files);
    const existingCount = $previewContainer.find('.preview-item').length;
    const remaining = maxPhotos - existingCount;
    if(remaining <= 0) return;

    files.slice(0, remaining).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const $div = $('<div>').addClass('preview-item');
            const $img = $('<img>').attr('src', e.target.result).attr('data-filename','');
            const $remove = $('<span>').addClass('remove-btn').text('×');
            $div.append($img).append($remove);
            $previewContainer.find('.add-photo').before($div);
            updateAddPhotoVisibility();
            adjustPreviewHeight();
        }
        reader.readAsDataURL(file);
    });

});

    function updateAddPhotoVisibility() {
        $previewContainer.find('.add-photo').css('display', $previewContainer.find('.preview-item').length >= maxPhotos ? 'none' : 'flex');
    }

    function adjustPreviewHeight() {
        $previewContainer.find('.preview-item, .add-photo').each(function() {
            $(this).css('height', $(this).outerWidth() + 'px');
        });
    }
    adjustPreviewHeight();
    $(window).on('resize', adjustPreviewHeight);

    // タグ
    const $tagsContainer = $('#tags');
    const $tagInput = $('#tag-input');
    const $tagCount = $('#tag-count');
    let tags = [];
    const existingGenres = $('#genres-input').val();
    if(existingGenres) { tags = JSON.parse(existingGenres); updateTags(); }

    function updateTags() {
        $tagsContainer.empty();
        tags.forEach((tag,i)=>{
            const $tagEl = $('<div>').addClass('tag').text(tag);
            $('<span>').text('×').on('click', function(){ tags.splice(i,1); updateTags(); }).appendTo($tagEl);
            $tagsContainer.append($tagEl);
        });
        $tagCount.text(tags.length + '/15');
    }

    $tagInput.on('keydown', function(e){
        if(e.key==='Enter'||e.key===','){ e.preventDefault(); if(tags.length<15) { tags.push($tagInput.val().trim()); $tagInput.val(''); updateTags(); } }
    });

    // 文字数カウント
    function setupCharCount(id,countId,maxLength){
        const $ta=$('#'+id), $count=$('#'+countId);
        $ta.on('input', function(){ if($ta.val().length>maxLength)$ta.val($ta.val().slice(0,maxLength)); $count.text($ta.val().length+'/'+maxLength); });
    }
    setupCharCount('overview','overview-count',40);
    setupCharCount('appeal','appeal-count',1000);

    // submit 前に hidden に保存
    $('form').on('submit', function(){
    $('#genres-input').val(JSON.stringify(tags));
    const photoOrder = [];
    $('#photo-preview .preview-item img').each(function(){
        const filename = $(this).data('filename');
        if (filename) {
            photoOrder.push(filename); // 既存ファイルのパス
        } else {
            photoOrder.push(null); // 新規ファイルは null（サーバー側で補完）
        }
    });
    $('#photo-order-input').val(JSON.stringify(photoOrder));
    });
});
</script>
@endsection
