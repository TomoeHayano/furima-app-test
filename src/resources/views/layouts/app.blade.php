<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 共通CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- ページごとの追加CSS --}}
    @stack('css')
</head>
<body class="{{ Request::is('login') || Request::is('register') || Request::is('email/verify') ? 'auth-body' : '' }}">
    <header class="auth-header">
        {{-- ロゴ --}}
        <div class="header-logo">
            <img src="{{ asset('images/logo.svg') }}" alt="CoachTech Logo">
        </div>

        @if (!Request::is('login') && !Request::is('register') && !Request::is('email/verify'))
            {{-- 検索フォーム --}}
            <form
                action="{{ Auth::check() ? route('products.mylist') : route('products.index') }}"
                method="GET"
                class="toppage-header-search"
            >
                <input 
                    type="text" 
                    name="keyword" 
                    placeholder="なにをお探しですか？"
                    value="{{ request('keyword') }}"
                >
                {{-- マイリストにいる場合は tab パラメータを維持 --}}
                @if (Auth::check() && request('tab') === 'mylist')
                    <input type="hidden" name="tab" value="mylist">
                @endif
            </form>

            {{-- ナビゲーション --}}
            <nav class="toppage-header-nav">
                @auth
                    {{-- ログアウト --}}
                    <form method="POST" action="{{ route('logout') }}" class="header-nav-item">
                        @csrf
                        <button type="submit" class="header-link logout-link">ログアウト</button>
                    </form>

                    {{-- マイページ --}}
                    <a href="#" class="header-link">マイページ</a>

                    {{-- 出品 --}}
                    <a href="#" class="header-button">出品</a>
                @endauth

                @guest
                    {{-- ログイン --}}
                    <a href="{{ route('login') }}" class="header-link">ログイン</a>

                    {{-- ゲストはログイン画面に誘導 --}}
                    <a href="{{ route('login') }}" class="header-link">マイページ</a>
                    <a href="{{ route('login') }}" class="header-button">出品</a>
                @endguest
            </nav>
        @endif
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
