@extends('layouts.header')

@section('title', '気になる一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/favorites.css') }}">
@endsection

@section('content')
<div class="stylist-list">
    <h2>気になるスタイリスト</h2>

    @if($favorites->isEmpty())
        <p>気になる登録したスタイリストはいません。</p>
    @else
        <div class="stylist-grid">
            @foreach($favorites as $stylist)
                <div class="stylist-card" onclick="window.location.href='{{ route('stylist.detail', $stylist->id) }}'">

                    {{-- 写真 --}}
                    @if(!empty($stylist->photos) && count($stylist->photos) > 0)
                        <img src="{{ asset('storage/' . $stylist->photos[0]) }}" alt="スタイリスト写真" class="stylist-photo">
                    @else
                        <div class="no-photo">No Image</div>
                    @endif

                    {{-- 概要 --}}
                    <h3>{{ $stylist->overview }}</h3>
                    <p class="appeal">{{ Str::limit($stylist->appeal, 60) }}</p>

                    {{-- ジャンルタグ --}}
                    <div class="tags">
                        @if(!empty($stylist->genres))
                            @foreach($stylist->genres as $genre)
                                <span class="tag">{{ $genre }}</span>
                            @endforeach
                        @endif
                    </div>

                    {{-- SNSリンク --}}
                    <div class="sns-links">
                        @if($stylist->twitter)
                            <a href="{{ $stylist->twitter }}" target="_blank"
                               onclick="event.stopPropagation()">Twitter</a>
                        @endif
                        @if($stylist->instagram)
                            <a href="{{ $stylist->instagram }}" target="_blank"
                               onclick="event.stopPropagation()">Instagram</a>
                        @endif
                    </div>

                    {{-- 価格 --}}
                    <p class="price">¥{{ number_format($stylist->price) }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection