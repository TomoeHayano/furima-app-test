@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endpush

@section('content')
@php
    $mode = $mode ?? 'guide';
@endphp

<div class="verify-container {{ $mode === 'guide' ? 'verify-container--guide' : 'verify-container--prompt' }}">
    <p class="verify-text">
        登録したメールアドレス宛に認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    @if ($mode === 'guide')
        <form method="GET" action="{{ route('verification.prompt') }}">
            <button type="submit" class="verify-button">認証はこちらから</button>
        </form>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="resend-button {{ $mode === 'guide' ? 'resend-button--guide' : 'resend-button--prompt' }}">認証メールを再送する</button>
    </form>

    @if (session('status'))
        <p class="verify-status {{ $mode === 'guide' ? 'verify-status--guide' : 'verify-status--prompt' }}">{{ session('status') }}</p>
    @endif
</div>
@endsection
