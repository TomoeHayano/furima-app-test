@extends('layouts.app')

@section('title', '会員登録')

@push('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

@section('content')
<div class="auth-content">
    <h1 class="auth-title">会員登録</h1>

    {{-- 会員登録フォーム --}}
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}">
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="text" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password">
            {{-- ここでは確認用のエラーを表示させない --}}
            @error('password')
                @if ($message !== 'パスワードと一致しません')
                    <p class="form-error">{{ $message }}</p>
                @endif
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">確認用パスワード</label>
            <input id="password-confirm" type="password" name="password_confirmation">

            {{-- 通常のエラー --}}
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror

            {{-- 確認用のエラー --}}
            @if ($errors->has('password') && $errors->first('password') === 'パスワードと一致しません')
                <p class="form-error">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <button type="submit" class="register-button">
            登録する
        </button>
    </form>

    <a href="{{ route('login') }}" class="login-link">ログインはこちら</a>
</div>
@endsection