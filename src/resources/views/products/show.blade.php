@extends('layouts.app')

@section('title', '商品詳細')

@push('css')
<link rel="stylesheet" href="{{ asset('css/products-show.css') }}">
@endpush

@section('content')
<div class="products-detail">
    {{-- 商品画像 --}}
    <div class="products-image-area">
        <div class="products-image">商品画像</div>
    </div>

    <div class="products-content">

        {{-- 商品タイトル・価格・いいね --}}
        <div class="products-title">
            <h1>{{ $product['name'] }}</h1>
            <p class="brand">{{ $product['brand'] }}</p>
            <p class="price">¥{{ number_format($product['price']) }} (税込)</p>

            <div class="products-actions">
                {{-- いいねアイコン（見た目だけ切替） --}}
                <span class="like-icon {{ $product['liked'] ? 'liked' : '' }}">★</span>
                <span>{{ $product['likes'] }}</span>
                {{-- コメントアイコン --}}
                <span class="comment-icon">💬</span>
                <span>{{ count($product['comments']) }}</span>
            </div>

            {{-- 購入ボタン --}}
            <div class="purchase-area">
                <button class="purchase-button">購入手続きへ</button>
            </div>
        </div>

        {{-- 商品説明 --}}
        <div class="products-description">
            <h2>商品説明</h2>
            <p>{{ $product['description'] }}</p>
        </div>

        {{-- 商品の情報 --}}
        <div class="products-info">
            <h2>商品の情報</h2>
            <p><strong>カテゴリー:</strong> {{ implode(', ', $product['categories']) }}</p>
            <p><strong>商品の状態:</strong> {{ $product['condition'] }}</p>
        </div>

        {{-- コメント --}}
        <div class="products-comments">
            <h2>コメント ({{ count($product['comments']) }})</h2>
            @foreach ($product['comments'] as $comment)
                <div class="comment-item">
                    <span class="comment-user">{{ $comment['user'] }}</span>:
                    <span class="comment-text">{{ $comment['content'] }}</span>
                </div>
            @endforeach

            {{-- コメント入力欄（見た目だけ） --}}
            <div class="comment-input">
                <textarea placeholder="商品のコメントを入力"></textarea>
                <button class="comment-submit">コメントを送信する</button>
            </div>
        </div>
    </div>
</div>
@endsection
