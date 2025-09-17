@extends('layouts.header')

@section('title', 'スタイリスト一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stylist_list.css') }}">
@endsection

@section('content')
<div class="stylist-list">
    <h2>スタイリスト一覧</h2>

    <div class="stylist-grid">
        @foreach($stylists as $stylist)
        <div class="stylist-card" onclick="window.location.href='{{ route('stylist.detail', $stylist->id) }}'">

            @if(!empty($stylist->photos) && count($stylist->photos) > 0)
                <img src="{{ asset('storage/' . $stylist->photos[0]) }}" alt="スタイリスト写真" class="stylist-photo">
            @else
                <div class="no-photo">No Image</div>
            @endif

            <h3>{{ $stylist->overview }}</h3>
            <p class="appeal">{{ Str::limit($stylist->appeal, 60) }}</p>

            <div class="tags">
                @if(!empty($stylist->genres))
                    @foreach($stylist->genres as $genre)
                        <span class="tag">{{ $genre }}</span>
                    @endforeach
                @endif
            </div>

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

            <p class="price">¥{{ number_format($stylist->price) }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
