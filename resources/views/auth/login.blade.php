@extends('layouts.header')

@section('title', 'ログイン')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')

<div class="form-container">
    <h1>ログイン</h1>

    <!-- ソーシャルログインボタン -->
    <div class="social-buttons">
        <button class="google">Googleでログイン</button>
    </div>

    <div class="divider">または</div>

    <!-- ログインフォーム -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password">パスワード</label>
        <input id="password" type="password" name="password" required>

        <div style="margin-bottom: 16px;">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">ログイン状態を保持する</label>
        </div>

        <button type="submit">ログイン</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        アカウントをお持ちでない方は <a href="{{ route('register') }}">登録</a>
    </p>
</div>
@endsection
