<header class="site-header">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@yield('css')
    <div class="logo">
        <a href="{{ url('/') }}">Coordi Match</a>
    </div>

    <nav class="site-nav">
        {{-- ゲスト用ナビ --}}
        @guest
            <ul id="nav-guest">
                <li><a href="#">スタイリスト一覧</a></li>
                <li><a href="{{ route('register') }}">会員登録</a></li>
                <li><a href="{{ route('login') }}">ログイン</a></li>
            </ul>
        @endguest

        {{-- ログイン済み用ナビ --}}
        @auth
            <ul id="nav-user">
                <li class="dropdown">
                    <a href="#" id="user-nickname">
                        {{ Auth::user()->nickname ?? 'ユーザー名' }} ▾
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">マイページ</a></li>
                        <li><a href="{{ route('user.profile.edit') }}">プロフィール</a></li>
                        <li><a href="{{ route('become.stylist') }}" id="become-stylist-btn">スタイリストになる</a></li>
                        <li>
                        <!-- 非表示フォーム -->
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                    <!-- 見た目は <a> のまま -->
                    <a href="{{ route('logout') }}"onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        ログアウト
                    </a>
                            </form>
                        </li>
                    </ul>
                </li>
                <li><a href="{{ route('stylist.list') }}">スタイリスト一覧</a></li>
                <li><a href="#"><span class="icon">🔔</span></a></li>
                <li><a href="#"><span class="icon">✅</span></a></li>
                <li><a href="#"><span class="icon">⭐ 気になる</span></a></li>
            </ul>
        @endauth
    </nav>
</header>
<main>
        @yield('content')
</main>
