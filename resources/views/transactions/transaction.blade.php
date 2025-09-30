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

        {{-- ユーザー側にだけ表示 --}}
    @if($trade->status === 'approved' && $trade->user_id === auth()->id() && !$trade->completed_by_user)
        <button type="button" class="btn btn-danger end-trade-btn">取引を終了する</button>

        <!-- モーダル -->
        <div id="endTradeModal" class="modal">
            <div class="modal-content">
                <h3>本当に取引を終了しますか？</h3>
                <form action="{{ route('trades.requestComplete', $trade->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">終了申請する</button>
                    <button type="button" class="btn btn-secondary modal-close">キャンセル</button>
                </form>
            </div>
        </div>
    @endif

    {{-- スタイリストにだけ表示（ユーザーが申請したら） --}}
    @if($trade->status === 'approved' && $trade->stylist->user_id === auth()->id() && $trade->completed_by_user && !$trade->completed_by_stylist)
        <button type="button" class="btn btn-primary complete-btn">取引終了を承認する</button>

        <!-- モーダル -->
        <div id="completeModal" class="modal">
            <div class="modal-content">
                <h3>取引終了を承認しますか？</h3>
                <form action="{{ route('trades.confirmComplete', $trade->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">承認する</button>
                    <button type="button" class="btn btn-secondary modal-close">キャンセル</button>
                </form>
            </div>
        </div>
    @endif

    {{-- 完全終了後 --}}
    @if($trade->status === 'completed')
        <p class="completed-message">この取引は終了しました。</p>
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
    @if(
        $trade->status === 'approved' || 
        ($trade->status === 'completed' && $trade->completed_at && $trade->completed_at->gt(now()->subWeeks(2)))
    )
        {{-- メッセージ一覧は常に表示 --}}
        @include('transactions.partials.message_section', ['trade' => $trade, 'messages' => $messages])

        {{-- 入力フォームは「承認中」のみ表示 --}}
        @if($trade->status === 'approved')
            @include('transactions.partials.message_form', ['trade' => $trade])
        @endif
    @elseif($trade->status === 'completed')
        {{-- 2週間を過ぎたら非表示 --}}
        <p class="completed-message">この取引は終了しました。（メッセージ閲覧期限は終了しました）</p>
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

    // approveModal を閉じる
    $('#approveModal .modal-close').on('click', function () {
        $('#approveModal').removeClass('show');
    });

    // endTradeModal を閉じる
    $('#endTradeModal .modal-close').on('click', function () {
        $('#endTradeModal').removeClass('show');
    });

    // completeModal を開く
    $('.complete-btn').on('click', function () {
        $('#completeModal').addClass('show');
    });

    // completeModal を閉じる
    $('#completeModal .modal-close').on('click', function () {
        $('#completeModal').removeClass('show');
    });

    $('.end-trade-btn').on('click', function(){
    $('#endTradeModal').addClass('show');
});
});
</script>
@endsection