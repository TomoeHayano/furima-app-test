@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endpush

@section('content')
<div class="verify-container">
    
    <p class="verify-text">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <form method="GET" action="{{ route('verification.verify', ['id' => Auth::id(), 'hash' => sha1(Auth::user()->email)]) }}">
        <button type="submit" class="verify-button">認証はこちらから</button>
    </form>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="resend-button">認証メールを再送する</button>
    </form>
</div>
@endsection