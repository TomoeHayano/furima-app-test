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
            <h1>{{ $product->name }}</h1>
            <p class="brand">{{ $product->brand_name }}</p>
            <p class="price">¥{{ number_format($product->price) }} (税込)</p>

            <div class="products-actions">
                {{-- いいねアイコン --}}
                <span
                    id="like-button"
                    class="like-icon {{ $liked ? 'liked' : '' }} {{ Auth::guest() ? 'disabled' : '' }}"
                    data-product-id="{{ $product['id'] }}">
                    ★
                </span>
                <span id="likes-count">{{ $likesCount }}</span>

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
            <p>{{ $product->description }}</p>
        </div>

        {{-- 商品の情報 --}}
        <div class="products-info">
            <h2>商品の情報</h2>
            <p><strong>カテゴリー:</strong> {{ implode(', ', $product->categories->pluck('name')->toArray()) }}</p>
            <p><strong>商品の状態:</strong> {{ $product->condition->status_name }}</p>
        </div>

        {{-- コメント --}}
        <div class="products-comments">
            <h2>コメント ({{ count($product->comments) }})</h2>
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

{{-- JSで非同期いいね処理 --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const likeButton = document.getElementById('like-button');
    const likesCount = document.getElementById('likes-count');

    if (likeButton) {
        // ゲストはクリックできない
        if (likeButton.classList.contains('disabled')) {
            return;
        }

        likeButton.addEventListener('click', function () {
            const productId = this.dataset.productId;

            fetch(`/item/${productId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'liked') {
                    likeButton.classList.add('liked');
                } else {
                    likeButton.classList.remove('liked');
                }
                likesCount.textContent = data.likesCount;
            })
            .catch(error => console.error('Error:', error));
        });
    }
});
</script>
@endsection
