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
@endsection