<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>

    </style>
    @stack('css')
</head>
<body>
    <header class="auth-header">
        <img src="coachtech-furima-app/logo.svg" alt="CoachTech_White Logo">
    </header>

    @yield('content')

</body>
</html>
