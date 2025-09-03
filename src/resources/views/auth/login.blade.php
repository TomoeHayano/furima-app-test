@section('title', 'ログイン')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="auth-content">
    <h1 class="auth-title">ログイン</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password" required>
        </div>

        <button type="submit" class="login-button">
            ログインする
        </button>
    </form>

    <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
</div>
@endsection
