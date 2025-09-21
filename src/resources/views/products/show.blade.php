@extends('layouts.app')

@section('title', '商品詳細')

@push('css')
<link rel="stylesheet" href="{{ asset('css/products-show.css') }}">
@endpush

@section('content')
<div class="products-detail">
    {{-- 左：商品画像 --}}
    <div class="products-image-area">
        <div class="products-image">
            @if($product->image_path)
                <img src="{{ $product->image_path }}" alt="{{ $product->name }}" style="max-width:100%; max-height:100%; object-fit:cover;">
            @else
                商品画像
            @endif
        </div>
    </div>

    {{-- 右：商品詳細 --}}
    <div class="products-content">

        {{-- 商品タイトル・ブランド・価格 --}}
        <div class="products-title">
            <h1>{{ $product->name }}</h1>
            <p class="brand">{{ $product->brand_name }}</p>
            <p class="price">¥{{ number_format($product->price) }} (税込)</p>

            {{-- いいね・コメント --}}
            <div class="products-actions">
                <span
                    id="like-button"
                    class="like-icon {{ $liked ? 'liked' : '' }} {{ Auth::guest() ? 'disabled' : '' }}"
                    data-product-id="{{ $product->id }}">
                    ★
                </span>
                <span id="likes-count">{{ $likesCount }}</span>

                <span class="comment-icon">💬</span>
                <span>{{ $product->comments->count() }}</span>
            </div>

            {{-- 購入ボタン --}}
            <div class="purchase-area">
                @auth
                    <form action="{{ route('purchase.create', $product->id) }}" method="GET">
                        <button type="submit" class="purchase-button">購入手続きへ</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="purchase-button">ログインして購入</a>
                @endauth
            </div>
        </div>

        {{-- 商品説明 --}}
        <div class="products-description">
            <h2>商品説明</h2>
            <p>{{ $product->description }}</p>
        </div>

        {{-- 商品情報 --}}
        <div class="products-info">
            <h2>商品の情報</h2>
            <p><strong>カテゴリー:</strong> {{ implode(', ', $product->categories->pluck('name')->toArray()) }}</p>
            <p><strong>商品の状態:</strong> {{ $product->condition->status_name }}</p>
        </div>

        {{-- コメント --}}
        <div class="products-comments">
            <h2>コメント ({{ $product->comments->count() }})</h2>

            {{-- コメント一覧 --}}
            @foreach ($product->comments as $comment)
                <div class="comment-item">
                    <span class="comment-user">{{ $comment->user->name }}</span>:
                    <span class="comment-text">{{ $comment->content }}</span>
                </div>
            @endforeach

            {{-- コメント入力欄（ログインユーザーのみ） --}}
            @auth
                <div class="comment-input">
                    <form action="{{ route('products.comment.store', $product->id) }}" method="POST">
                        @csrf
                        <textarea 
                            name="content" 
                            placeholder="商品のコメントを入力" 
                            maxlength="255">{{ old('content') }}</textarea>

                        {{-- バリデーションエラー表示 --}}
                        @error('content')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="comment-submit">コメントを送信する</button>
                    </form>
                </div>
            @endauth
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
