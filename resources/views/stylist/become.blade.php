@extends('layouts.header')

@section('title', 'スタイリスト登録')

@section('css')
<link rel="stylesheet" href="{{ asset('css/become_stylist.css') }}">
@endsection

@section('content')
<div class="stylist-form">
    <h2>スタイリスト登録</h2>

    <!-- 写真選択 -->
    <div class="form-group">
    <label>写真 (最大8枚)</label>

    {{-- プレビューエリア --}}
    <div id="photo-preview" class="photo-preview">
     {{-- 追加ボタン用の枠 --}}
        <label for="photos" class="add-photo">＋</label>
    </div>

    {{-- 実際のファイル入力（非表示） --}}
    <input type="file" id="photos" name="photos[]" multiple accept="image/*" style="display: none;">
    </div>

    {{-- スタイリスト概要 --}}
    <div class="form-group">
        <label for="appeal">スタイリスト概要</label>
        <textarea id="overview" maxlength="40" rows="2" placeholder="スタイリスト概要を入力（最大40文字）"></textarea>
        <div class="char-count" id="overview-count">0/40</div>
    </div>

    <!-- 得意ジャンル（タグ入力UI） -->
    <div class="form-group">
        <div class="input-wrapper">
            <label for="tags">得意ジャンル</label>
            <input type="text" id="tag-input" placeholder="タグを入力">
            <span id="tag-count">0/15</span>
            <div class="tags" id="tags"></div>
        </div>
    </div>



    <!-- アピール文 -->
    <div class="form-group">
        <label for="appeal">アピール文</label>
        <textarea id="appeal" maxlength="1000" rows="5" placeholder="アピール文を入力（最大1000文字）"></textarea>
        <div class="char-count" id="appeal-count">0/1000</div>
    </div>

    <!-- SNS -->
    <div class="form-group">
        <label for="twitter">Twitter</label>
        <input type="url" id="twitter" placeholder="https://twitter.com/xxxx">
    </div>
    <div class="form-group">
        <label for="instagram">Instagram</label>
        <input type="url" id="instagram" placeholder="https://instagram.com/xxxx">
    </div>

    <!-- 価格 -->
    <div class="form-group">
        <label for="price">価格</label>
        <input type="number" id="price" placeholder="例: 3000">
    </div>

    <!-- ボタン -->
    <div class="form-actions">
        <button type="submit" class="btn-primary">スタイリストになる</button>
        <button type="button" class="btn-secondary">下書き保存</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(function () {
    const $inputPhotos = $('#photos');
    const $previewContainer = $('#photo-preview');
    const maxPhotos = 8;

    // Sortable 初期化
    new Sortable($previewContainer[0], {
        animation: 150,
        ghostClass: 'sortable-ghost'
    });

    // ファイル追加
    $inputPhotos.on('change', function (event) {
        const files = Array.from(event.target.files);
        const existingCount = $previewContainer.find('.preview-item').length;
        const remainingSlots = maxPhotos - existingCount;

        files.slice(0, remainingSlots).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const $div = $('<div>').addClass('preview-item');
                const $img = $('<img>').attr('src', e.target.result);
                const $removeBtn = $('<span>')
                    .addClass('remove-btn')
                    .text('×')
                    .on('click', function () {
                        $div.remove();
                        updateAddPhotoVisibility();
                        adjustPreviewHeight();
                    });

                $div.append($img).append($removeBtn);
                $previewContainer.find('.add-photo').before($div);

                updateAddPhotoVisibility();
                adjustPreviewHeight();
            };
            reader.readAsDataURL(file);
        });

        $(this).val(''); // inputをリセット
    });

    // 追加ボタンの表示制御
    function updateAddPhotoVisibility() {
        const $addBtn = $previewContainer.find('.add-photo');
        $addBtn.css('display', $previewContainer.find('.preview-item').length >= maxPhotos ? 'none' : 'flex');
    }

    // プレビュー枠を正方形に調整
    function adjustPreviewHeight() {
        $previewContainer.find('.preview-item, .add-photo').each(function () {
            const width = $(this).outerWidth();
            $(this).css('height', width + 'px');
        });
    }

    // 初期調整
    adjustPreviewHeight();
    $(window).on('resize', adjustPreviewHeight);

    /* ==============================
       タグ入力機能
    ============================== */
    const $input = $('#tag-input');
    const $tagsContainer = $('#tags');
    const $tagCount = $('#tag-count');
    let tags = [];
    const maxTags = 15;

    function updateTags() {
        $tagsContainer.empty();
        $.each(tags, function (index, tag) {
            const $tagEl = $('<div>').addClass('tag').text(tag);
            const $removeBtn = $('<span>')
                .text('×')
                .on('click', function () {
                    tags.splice(index, 1);
                    updateTags();
                });

            $tagEl.append($removeBtn);
            $tagsContainer.append($tagEl);
        });
        $tagCount.text(tags.length + '/' + maxTags);
    }

    function addTag(tag) {
        tag = tag.trim();
        if (tag && !tags.includes(tag) && tags.length < maxTags) {
            tags.push(tag);
            updateTags();
        }
    }

    $input.on('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag($input.val());
            $input.val('');
        }
    });

    /* ==============================
       文字数カウント制御
    ============================== */
    function setupCharCount(textareaId, countId, maxLength) {
        const $textarea = $('#' + textareaId);
        const $countDisplay = $('#' + countId);

        $textarea.on('input', function () {
            if ($textarea.val().length > maxLength) {
                $textarea.val($textarea.val().slice(0, maxLength));
            }
            $countDisplay.text($textarea.val().length + '/' + maxLength);
        });
    }

    // スタイリスト概要（40文字）
    setupCharCount('overview', 'overview-count', 40);

    // アピール（1000文字）
    setupCharCount('appeal', 'appeal-count', 1000);
});
</script>

@endsection
