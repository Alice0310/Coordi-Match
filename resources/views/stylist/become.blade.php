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
<script>
const inputPhotos = document.getElementById('photos');
const previewContainer = document.getElementById('photo-preview');

// Sortable 初期化（ドラッグ可能）
new Sortable(previewContainer, {
    animation: 150,
    ghostClass: 'sortable-ghost'
});

// ファイル追加
inputPhotos.addEventListener('change', function(event) {
    const files = Array.from(event.target.files);
    const existingCount = previewContainer.querySelectorAll('.preview-item').length;
    const remainingSlots = 8 - existingCount; // 残り枠

    files.slice(0, remainingSlots).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.classList.add('preview-item');

            const img = document.createElement('img');
            img.src = e.target.result;

            const removeBtn = document.createElement('span');
            removeBtn.classList.add('remove-btn');
            removeBtn.innerText = '×';
            removeBtn.addEventListener('click', () => {
                div.remove();
                updateAddPhotoVisibility();
                adjustPreviewHeight(); // 高さ調整が必要なら呼ぶ
            });

            div.appendChild(img);
            div.appendChild(removeBtn);

            const addBtn = previewContainer.querySelector('.add-photo');
            previewContainer.insertBefore(div, addBtn);

            updateAddPhotoVisibility();
            adjustPreviewHeight(); // 高さ調整が必要なら呼ぶ
        };
        reader.readAsDataURL(file);
    });

    event.target.value = ''; // inputをリセット
});


// 追加ボタンの表示制御
function updateAddPhotoVisibility() {
    const addBtn = previewContainer.querySelector('.add-photo');
    addBtn.style.display = previewContainer.querySelectorAll('.preview-item').length >= 8 ? 'none' : 'flex';
}

// プレビュー枠を正方形に調整
function adjustPreviewHeight() {
    const items = document.querySelectorAll('.preview-item, .add-photo');
    items.forEach(item => {
        const width = item.offsetWidth;
        item.style.height = width + 'px';
    });
}

// 初期調整
adjustPreviewHeight();
window.addEventListener('resize', adjustPreviewHeight);



    const input = document.getElementById("tag-input");
    const container = document.getElementById("tag-container");

    input.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            const value = input.value.trim();
            if (value) {
                const tag = document.createElement("span");
                tag.className = "tag";
                tag.textContent = value;
                container.appendChild(tag);
                input.value = "";
            }
        }
    });

    function setupCharCount(textareaId, countId, maxLength) {
    const textarea = document.getElementById(textareaId);
    const countDisplay = document.getElementById(countId);

    textarea.addEventListener('input', () => {
        if (textarea.value.length > maxLength) {
            textarea.value = textarea.value.slice(0, maxLength); // 強制カット
        }
        countDisplay.innerText = `${textarea.value.length}/${maxLength}`;
    });
}

// スタイリスト概要（40文字）
setupCharCount('overview', 'overview-count', 40);

// アピール（1000文字）
setupCharCount('appeal', 'appeal-count', 1000);

// タグ制限
const inpu = document.getElementById('tag-input'); //
const tagsContainer = document.getElementById('tags');
const tagCount = document.getElementById('tag-count');

let tags = [];
const maxTags = 15;

function updateTags() {
  tagsContainer.innerHTML = '';
  tags.forEach((tag, index) => {
    const tagEl = document.createElement('div');
    tagEl.classList.add('tag');
    tagEl.textContent = tag;

    const removeBtn = document.createElement('span');
    removeBtn.textContent = '×';
    removeBtn.onclick = () => {
      tags.splice(index, 1);
      updateTags();
    };

    tagEl.appendChild(removeBtn);
    tagsContainer.appendChild(tagEl);
  });
  tagCount.textContent = `${tags.length}/${maxTags}`;
}

function addTag(tag) {
  tag = tag.trim();
  if(tag && !tags.includes(tag) && tags.length < maxTags) {
    tags.push(tag);
    updateTags();
  }
}

input.addEventListener('keydown', function(e) {
  if(e.key === 'Enter' || e.key === ',') {
    e.preventDefault();          
    addTag(input.value);         // タグを追加
    input.value = '';            // 入力欄を空にする
  }
});



</script>
@endsection
