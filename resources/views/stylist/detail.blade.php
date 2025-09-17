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
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
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
        <p class="appeal">{{ $stylist->appeal }}</p>

        <div class="tags">
            @if(!empty($stylist->genres))
                @foreach($stylist->genres as $genre)
                    <span class="tag">{{ $genre }}</span>
                @endforeach
            @endif
        </div>

        <div class="sns-links">
            @if($stylist->twitter)
                <a href="{{ $stylist->twitter }}" target="_blank">Twitter</a>
            @endif
            @if($stylist->instagram)
                <a href="{{ $stylist->instagram }}" target="_blank">Instagram</a>
            @endif
        </div>

        <p class="price">¥{{ number_format($stylist->price) }}</p>
    </div>
</div>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  var thumbSwiper = new Swiper(".thumbSwiper", {
  spaceBetween: 10,
  slidesPerView: 4,
  watchSlidesProgress: true,
  navigation: {
    nextEl: ".thumb-next",
    prevEl: ".thumb-prev",
  },
});

  var mainSwiper = new Swiper(".mainSwiper", {
    loop: true,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: thumbSwiper,
    },
  });
  
</script>
@endsection
