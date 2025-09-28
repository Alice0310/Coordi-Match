@extends('layouts.header')

@section('title', $user->nickname . 'さんの持ち服一覧')

@section('content')
<div class="clothes-container">
    <h2>{{ $user->nickname }} さんの持ち服一覧</h2>

    <div class="clothes-list">
        @forelse($clothes as $cloth)
            <div class="cloth-item">
                <img src="{{ asset('storage/'.$cloth->photo_path) }}" alt="服" width="120">
            </div>
        @empty
            <p>まだ登録されていません。</p>
        @endforelse
    </div>
</div>
@endsection