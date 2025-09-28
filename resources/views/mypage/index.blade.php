@extends('layouts.header')

@section('title', 'マイページ')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <!-- 左側：メニュー -->
    <aside class="mypage-sidebar">
        <div class="profile-card">
            <img src="{{ Auth::user()->profile_image ?? '/images/default-avatar.png' }}" 
                 alt="プロフィール画像" class="profile-img">
            <h3>{{ Auth::user()->nickname }}</h3>
        </div>

        <ul class="menu-list">
            <li><a href="{{ route('user.profile.edit') }}">プロフィール編集</a></li>
            <li><a href="{{ route('clothes.index') }}">持ち服一覧</a></li>
            <li><a href="{{ route('notifications.index') }}">お知らせ</a></li>
            <li><a href="#">気になる</a></li>
            <li><a href="#">取引一覧</a></li>
        </ul>
    </aside>

    <!-- 右側：メインエリア -->
    <main class="mypage-main">
        

        {{-- 必要に応じて右側に section を切り替えて表示 --}}
        @yield('mypage-content')
    </main>
</div>
@endsection