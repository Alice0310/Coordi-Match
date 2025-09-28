@extends('layouts.header')

@section('title', '取引ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
@endsection

@section('content')
<div class="transaction-container">
    <h2>取引ページ</h2>

    <p>スタイリスト: {{ $trade->stylist->user->nickname ?? '不明' }}</p>
    <p>ユーザー: {{ $trade->user->nickname ?? $trade->user->name ?? '不明' }}</p>

    {{-- ログインユーザーがスタイリスト本人なら表示 --}}
    @if(auth()->id() === $trade->stylist->user_id)
        <a href="{{ route('clothes.user', $trade->user->id) }}" class="btn btn-secondary">
            このユーザーの持ち服一覧を見る
        </a>
    @endif

    <div id="alert-area"></div>

    @if($trade->status === 'pending')
    <div class="actions">
        <button type="button" class="btn btn-primary approve-btn">取引を承認する</button>
    </div>
    @endif
    
    <!-- モーダル: 承認確認 -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <h3>取引を開始しますか？</h3>
            <form action="{{ route('transactions.approve', $trade->id) }}" method="POST" class="approve-form">
                @csrf
                <button type="submit" class="btn btn-primary">開始する</button>
                <button type="button" class="btn btn-secondary modal-close">キャンセル</button>
            </form>
        </div>
    </div>

    <!-- メッセージ -->
    <div id="message-section">
        @if($trade->status === 'approved')
            @include('transactions.partials.message_section', ['trade' => $trade, 'messages' => $messages])
        @else
            <!-- 承認後にメッセージ欄を表示 -->
        @endif
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(function() {
    // モーダル表示
    $('.approve-btn').on('click', function (e) {
        e.preventDefault();
        $('#approveModal').addClass('show');
    });

    // モーダル閉じる
    $('.modal-close').on('click', function () {
        $('#approveModal').removeClass('show');
    });

    // 承認フォーム送信
    $('.approve-form').on('submit', function (e) {
    e.preventDefault();

    $.post($(this).attr('action'), $(this).serialize())
      .done(function (res) {
        $('#approveModal').removeClass('show');
        $('.approve-btn').remove();
        // メッセージ欄をレスポンスで置き換え
        $('#message-section').replaceWith(res.html);

        $('#alert-area').html(`
          <div class="alert alert-success">${res.message}</div>
        `);
      })
      .fail(function () {
        alert('承認に失敗しました。');
      });
  });
});
</script>
@endsection