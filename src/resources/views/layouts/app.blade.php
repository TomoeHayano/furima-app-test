<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    {{-- 共通CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- ページごとの追加CSS --}}
    @stack('css')
</head>
<body class="{{ Request::is('login') || Request::is('register') ? 'auth-body' : '' }}">
    
    <header class="auth-header">
        {{-- ロゴ --}}
        <div class="header-logo">
            <img src="{{ asset('images/logo.svg') }}" alt="CoachTech Logo">
        </div>
        <nav class="toppage-header-nav">
            @auth
                {{-- ログアウト --}}
                <form method="POST" action="{{ route('logout') }}" class="header-nav-item">
                    @csrf
                    <button type="submit" class="header-link logout-link">ログアウト</button>
                </form>

                {{-- マイページ（ログイン後のみ実際のページに飛ぶようにする） --}}
                <a href="#" class="header-link">マイページ</a>

                {{-- 出品 --}}
                <a href="#" class="header-button">出品</a>
            @endauth

            @guest
                {{-- ログイン --}}
                <a href="{{ route('login') }}" class="header-link">ログイン</a>

                {{-- ゲストは全部ログイン画面に飛ばす --}}
                <a href="{{ route('login') }}" class="header-link">マイページ</a>
                <a href="{{ route('login') }}" class="header-button">出品</a>
            @endguest
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
