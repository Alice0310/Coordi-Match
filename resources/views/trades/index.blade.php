@extends('layouts.header')

@section('title', '取引一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trades.css') }}">
@endsection

@section('content')
<div class="trades-container">
    <h2>取引一覧</h2>

    <h3>進行中の取引</h3>
    @if($ongoingTrades->isEmpty())
        <p>現在、進行中の取引はありません。</p>
    @else
        <ul class="trade-list">
            @foreach($ongoingTrades as $trade)
                <li class="trade-item">
                    <a href="{{ route('trades.show', $trade->id) }}">
                        <div>
                            <strong>スタイリスト:</strong> {{ $trade->stylist->user->nickname ?? '不明' }}<br>
                            <strong>ユーザー:</strong> {{ $trade->user->nickname ?? $trade->user->name ?? '不明' }}<br>
                            <strong>ステータス:</strong>
                            @if($trade->status === 'pending') 申請中
                            @elseif($trade->status === 'approved') 進行中
                            @endif
                        </div>
                        <span class="time">{{ $trade->created_at->diffForHumans() }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <h3>完了した取引</h3>
    @if($completedTrades->isEmpty())
        <p>完了した取引はありません。</p>
    @else
        <ul class="trade-list">
            @foreach($completedTrades as $trade)
                <li class="trade-item">
                    <a href="{{ route('trades.show', $trade->id) }}">
                        <div>
                            <strong>スタイリスト:</strong> {{ $trade->stylist->user->nickname ?? '不明' }}<br>
                            <strong>ユーザー:</strong> {{ $trade->user->nickname ?? $trade->user->name ?? '不明' }}<br>
                            <strong>完了日:</strong> {{ optional($trade->completed_at)->format('Y/m/d') }}
                        </div>
                        <span class="time">{{ $trade->created_at->diffForHumans() }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection