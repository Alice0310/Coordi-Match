@extends('layouts.header')

@section('title', '会員登録')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')

<div class="form-container">
    <h1>会員登録</h1>

    <!-- ソーシャルログインボタン -->
    <div class="social-buttons">
        <button class="google">Googleで登録</button>
        <button class="facebook">Facebookで登録</button>
        <button class="line">LINEで登録</button>
    </div>

    <div class="divider">または</div>

    <!-- 登録フォーム -->
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">名前</label>
        <input id="name" type="text" name="name" value="{{ old('name','') }}" required autofocus>

        <label for="nickname">ニックネーム</label>
        <input id="nickname" type="text" name="nickname" value="{{ old('nickname') }}" required autofocus>

        <!-- エラー表示 -->
        @error('nickname')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <label for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>

        <label for="password">パスワード</label>
        <input id="password" type="password" name="password" required>

        <label for="password_confirmation">パスワード確認</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>

        <button type="submit">登録する</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        すでにアカウントをお持ちですか？ <a href="{{ route('login') }}">ログイン</a>
    </p>
</div>
@endsection
