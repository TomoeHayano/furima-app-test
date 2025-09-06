@extends('layouts.app')

@section('title', 'ログイン')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="auth-content">
    <h1 class="auth-title">ログイン</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group @error('email') has-error @enderror">
            <label for="email">メールアドレス</label>
            <input id="email" type="text" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

       <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="login-button">
            ログインする
        </button>
    </form>

    <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
</div>
@endsection
