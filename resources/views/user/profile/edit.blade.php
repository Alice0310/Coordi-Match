@extends('layouts.header')

@section('title', 'プロフィール編集')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="mypage-form">
    <h2>プロフィールを編集</h2>

        <!-- フラッシュメッセージ -->
    @if (session('success'))
        <div style="color: green; margin-bottom: 10px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('status'))
        <div style="color:green; margin-bottom:10px;">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div id="preview-container" class="profile-image-area">
            <div class="circle-preview">
                @if ($user->profile_image_path)
                    <img id="profilePreview" src="{{ asset('storage/' . $user->profile_image_path) }}" alt="画像プレビュー">
                @else
                    <img id="profilePreview" src="" alt="画像プレビュー" style="display:none;">
                @endif
            </div>
            <label for="profileImage" class="image-upload-button">画像を選択</label>
            <input type="file" id="profileImage" name="profile_image" accept="image/*" style="display:none;">
        </div>

        <label for="nickname">ニックネーム:</label><br>
        <input type="text" id="nickname" name="nickname" value="{{ old('nickname', $user->nickname) }}" required><br><br>

        <label for="description">自己紹介:</label><br>
        <textarea id="description" name="description" rows="5" cols="30" required>{{ old('description', $user->description) }}</textarea><br><br>

        <button type="submit">保存</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $('#profileImage').on('change', function (event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          $('#profilePreview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
      }
    });
</script>
@endsection
