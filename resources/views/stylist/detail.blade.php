@extends('layouts.header')

@section('title', 'スタイリスト詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stylist_detail.css') }}">
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endsection

@section('content')
<div class="stylist-detail-container">
    <!-- 左側: 写真スライダー -->
    <div class="left-side">
        @if(!empty($stylist->photos) && count($stylist->photos) > 0)
            <!-- メインスライダー -->
            <div class="swiper mainSwiper">
                <div class="swiper-wrapper">
                    @foreach($stylist->photos as $photo)
                        <div class="swiper-slide">
                            <img src="{{ asset('storage/' . $photo) }}" alt="スタイリスト写真" class="main-photo">
                        </div>
                    @endforeach
                </div>
                <!-- ナビゲーション -->
                <div class="swiper-button-next main-next"></div>
                <div class="swiper-button-prev main-prev"></div>
            </div>

            <!-- サムネイルスライダー -->
            <div class="swiper thumbSwiper">
                <div class="swiper-wrapper">
                    @foreach($stylist->photos as $photo)
                        <div class="swiper-slide">
                            <img src="{{ asset('storage/' . $photo) }}" alt="スタイリスト写真" class="thumb-photo">
                        </div>
                    @endforeach
                </div>
                <!-- ナビゲーション矢印 -->
                <div class="swiper-button-next thumb-next"></div>
                <div class="swiper-button-prev thumb-prev"></div>
            </div>
        @else
            <div class="no-photo">No Image</div>
        @endif
    </div>

    <!-- 右側: 詳細情報 -->
    <div class="right-side">
        <h2>{{ $stylist->overview }}</h2>

        <h4 class="section-title">得意ジャンル</h4>

        <div class="tags">
            @if(!empty($stylist->genres))
                @foreach($stylist->genres as $genre)
                    <span class="tag">{{ $genre }}</span>
                @endforeach
            @endif
        </div>

        <p class="price">¥{{ number_format($stylist->price) }}</p>

            <!-- ボタン -->
        <div class="action-buttons">
        @if($stylist->user_id === auth()->id())
        <!-- 自分のスタイリストなら編集ボタン -->
        <a href="{{ route('become.stylist') }}" class="btn btn-primary apply-btn">スタイリスト編集</a>
    @else
        <!-- 他人のスタイリストなら取引手続き申請ボタン -->
        <button type="button" class="btn btn-primary apply-btn">取引手続き申請</button>
    @endif

        <!-- 気になる と コメント は常に表示 -->
        <button type="button" class="btn btn-secondary favorite-btn">気になる</button>
        <button type="button" class="btn btn-outline comment-btn">コメント</button>
        </div>

            <!-- モーダル -->
        <div id="tradeConfirmModal" class="modal">
            <div class="modal-content">
                <h3>{{ $stylist->overview }} さんに取引申請しますか？</h3>
                <form action="{{ route('trade.request', $stylist->id) }}" method="POST" class="trade-form">
                @csrf
                <button type="submit" class="btn btn-primary">申請する</button>
                <button type="button" class="btn btn-secondary modal-close">キャンセル</button>
                </form>
            </div>
        </div>

    <!-- 通知表示用エリア -->
    <div id="alert-area"></div>

        <div class="sns-links">
            @if($stylist->twitter)
                <a href="{{ $stylist->twitter }}" target="_blank">Twitter</a>
            @endif
            @if($stylist->instagram)
                <a href="{{ $stylist->instagram }}" target="_blank">Instagram</a>
            @endif
        </div>

        <p class="appeal">{{ $stylist->appeal }}</p>

            <!-- コメント欄 -->
    <div id="comment-section" class="comment-section">
        <h4>コメント</h4>
        <form action="{{ route('stylist.comment', $stylist->id) }}" method="POST">
            @csrf
            <textarea name="comment" rows="4" placeholder="コメントを入力..." required></textarea>
            <button type="submit" class="btn btn-sm btn-primary">送信</button>
        </form>

        <!-- 既存コメント一覧 -->
        @foreach($stylist->comments ?? [] as $comment)
            <div class="comment-item">
                <strong>{{ $comment->user->name }}</strong>: {{ $comment->body }}

                <!-- スタイリスト本人だけに削除ボタンを表示 -->
                @if(auth()->check() && auth()->id() === $stylist->user_id)
                    <button type="button" 
                            class="btn btn-sm btn-danger delete-btn" 
                            data-id="{{ $comment->id }}">
                        削除
                    </button>
                @endif
            </div>
        @endforeach
        </div>

        <!-- コメント削除確認モーダル -->
        <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <h3>本当に削除しますか？</h3>
            <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">削除する</button>
            <button type="button" class="btn btn-secondary modal-close">キャンセル</button>
            </form>
        </div>
    </div>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(function() {
  // Swiper
  const thumbSwiper = new Swiper('.thumbSwiper', {
    spaceBetween: 10,
    slidesPerView: 5,
    freeMode: true,
    watchSlidesProgress: true,
    navigation: { nextEl: '.thumb-next', prevEl: '.thumb-prev' },
  });

  const mainSwiper = new Swiper('.mainSwiper', {
    spaceBetween: 10,
    navigation: { nextEl: '.main-next', prevEl: '.main-prev' },
    thumbs: { swiper: thumbSwiper },
  });

  // コメントボタン
  $('.comment-btn').on('click', function(e) {
    e.preventDefault();
    $('#comment-section')[0].scrollIntoView({ behavior: 'smooth' });
  });

  // 取引申請ボタン → モーダル表示
$('.apply-btn').on('click', function (e) {
  e.preventDefault();
  $('#tradeConfirmModal').addClass('show');
});

// キャンセルボタン → モーダル非表示
$('.modal-close').on('click', function () {
  $('#tradeConfirmModal').removeClass('show');
});

// 背景クリックでも非表示
$('#tradeConfirmModal').on('click', function (e) {
  if (e.target === this) {
    $(this).removeClass('show');
  }
});

// 取引申請フォームをAjax送信
$('.trade-form').on('submit', function (e) {
  e.preventDefault(); // フォーム送信を止める

  $.post($(this).attr('action'), $(this).serialize())
    .done(function () {
      $('#tradeConfirmModal').removeClass('show'); // ← hide()ではなく removeClass に統一
      $('#alert-area').html(`
        <div class="alert alert-success">
          申請しました。スタイリストからの承認をお待ちください。
        </div>
      `);
    })
    .fail(function () {
      $('#alert-area').html(`
        <div class="alert alert-danger">
          申請に失敗しました。もう一度お試しください。
        </div>
      `);
    });
  });
});

// コメント削除ボタンを押したらモーダルを表示
$('.delete-btn').on('click', function() {
  const commentId = $(this).data('id');
  $('#deleteForm').attr('action', '/comments/' + commentId);
  $('#deleteConfirmModal').addClass('show');
});

// キャンセル → モーダル非表示
$('#deleteConfirmModal .modal-close').on('click', function() {
  $('#deleteConfirmModal').removeClass('show');
});

// 背景クリックでも閉じる
$('#deleteConfirmModal').on('click', function(e) {
  if (e.target === this) {
    $(this).removeClass('show');
  }
});

  
</script>
@endsection
