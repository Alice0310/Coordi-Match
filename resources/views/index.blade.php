@extends('layouts.header')

@section('title', 'CoordiMatch')

@section('css')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
<section class="hero">
    <div class="hero-inner">
        <h1 class="hero-title">CordiMatch</h1>
        <p class="hero-subtitle">コーディネートをもっと楽しく、もっと身近に。</p>
        <div class="hero-buttons">
            {{-- 未ログイン時 --}}
            @guest
                <a href="{{ route('register') }}" class="btn btn-accent">会員登録</a>
                <a href="{{ route('login') }}" class="btn btn-outline">ログイン</a>
            @endguest

            {{-- ログイン時 --}}
            @auth
                <a href="{{ url('/stylist/become') }}" class="btn btn-accent">スタイリストになる</a>
            @endauth
        </div>
    </div>
</section>

{{-- How To セクション --}}
<section class="howto">
    <div class="container">
        <h2>ご利用の流れ</h2>
        <div class="steps">
            <div class="step">
                <span class="step-number">1</span>
                <h3>会員登録</h3>
                <p>メールアドレスとパスワードで簡単に登録できます。</p>
            </div>
            <div class="step">
                <span class="step-number">2</span>
                <h3>プロフィール設定</h3>
                <p>プロフィールや持ち服を登録して、準備完了！</p>
            </div>
            <div class="step">
                <span class="step-number">3</span>
                <h3>スタイリストを探す</h3>
                <p>ジャンルや雰囲気からあなたに合うスタイリストを見つけましょう。</p>
            </div>
            <div class="step">
                <span class="step-number">4</span>
                <h3>取引を開始</h3>
                <p>取引申請をして、チャットで相談しながらコーディネート依頼！</p>
            </div>
        </div>
    </div>
</section>
@endsection