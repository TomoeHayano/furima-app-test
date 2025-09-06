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
<body>
    <header class="auth-header">
        <img src="{{ asset('images/logo.svg') }}" alt="CoachTech Logo">
    </header>

    @yield('content')

</body>
</html>
