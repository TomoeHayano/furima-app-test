@extends('layouts.auth')

@section('title', '会員登録')

@section('content')
<style>

</style>
<div class="auth-content">
    <h1>会員登録</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input id="name" type="text" name="name" required autofocus>
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password-confirm">確認用パスワード</label>
            <input id="password-confirm" type="password" name="password_confirmation" required>
        </div>

        <button type="submit" class="register-button">
            登録する
        </button>
    </form>

    <a href="{{ route('login') }}" class="login-link">ログインはこちら</a>
</div>
@endsection
