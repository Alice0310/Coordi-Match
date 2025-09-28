@extends('layouts.header')

@section('title', 'お知らせ')

@section('css')
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endsection

@section('content')
<div class="notifications-container">
    <h2>お知らせ</h2>

    @forelse($notifications as $notification)
    @if($notification->type === 'trade_request' || $notification->type === 'trade_approved')
        {{-- 取引関連はリンク付き --}}
        <a href="{{ route('trades.show', $notification->data['trade_id']) }}"
           class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}">
            <p class="message">{{ $notification->data['message'] }}</p>
            <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
        </a>
    @else
        {{-- コメントや気になる通知 --}}
        <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}">
            <p class="message">{{ $notification->data['message'] }}</p>
            <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
        </div>
    @endif
    @empty
        <p class="no-notifications">お知らせはありません。</p>
    @endforelse
</div>
@endsection
