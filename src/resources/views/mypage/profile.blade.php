@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile-container">

    <!-- ユーザー情報 -->
    <div class="user-info">
        <div class="user-avatar">
            @if ($user->profile && $user->profile->image_path)
                <img src="{{ asset('storage/' . $user->profile->image_path) }}" alt="プロフィール画像">
            @endif
        </div>
        <div class="user-details">
            <div class="user-meta">
                <h1 class="user-name">{{ $user->name }}</h1>
                @php
                    $hasRating = !is_null($ratingAverage);
                    $displayRating = $hasRating ? $ratingAverage : 0;
                    $ariaLabel = $hasRating ? "評価 {$ratingAverage} / 5" : '評価なし';
                @endphp
                <div class="user-rating" aria-label="{{ $ariaLabel }}">
                    @for ($i = 1; $i <= 5; $i++)
                        @php
                            $isActive = $i <= $displayRating;
                            $starColor = $isActive ? '#fff048' : '#d9d9d9';
                        @endphp
                        <span
                            class="user-rating__star"
                            aria-hidden="true"
                            style="
                                color: {{ $starColor }};
                                width: 40px;
                                height: 40px;
                                font-size: 32px;
                                line-height: 40px;
                                display: inline-block;
                                text-align: center;
                            "
                        >★</span>
                    @endfor
                </div>
            </div>
            <a href="{{ route('user.profile.edit') }}" class="profile-edit-button">プロフィールを編集</a>
        </div>
    </div>
    
    <!-- タブセクション -->
    <div class="tab-section">
        <div class="tab-list">
            <a href="{{ route('user.mypage', ['page' => 'sell']) }}"
               class="tab-item {{ $page === 'sell' ? 'active' : '' }}">
                出品した商品
            </a>
            <a href="{{ route('user.mypage', ['page' => 'buy']) }}"
               class="tab-item {{ $page === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
            <a href="{{ route('user.mypage', ['page' => 'progress']) }}"
               class="tab-item {{ $page === 'progress' ? 'active' : '' }}">
                取引中の商品
                @if (!empty($progressUnreadTotal))
                    <span class="tab-badge">{{ $progressUnreadTotal }}</span>
                @endif
            </a>
        </div>
        <div class="tab-underline"></div>
    </div>
    
    <!-- 商品一覧 -->
    <div class="products-grid">
        @if ($page === 'progress')
            @forelse ($transactions as $transaction)
                @php
                    $product = $transaction->order->product;
                    $imageUrl = null;
                    if ($product->image_path) {
                        $imagePath = $product->image_path;
                        if (preg_match('/^https?:\\/\\//', $imagePath)) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, '/storage/')) {
                            $imageUrl = asset(ltrim($imagePath, '/'));
                        } elseif (str_starts_with($imagePath, 'storage/')) {
                            $imageUrl = asset($imagePath);
                        } else {
                            $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
                        }
                    }
                @endphp
                <div class="product-card">
                    <div class="product-image-wrapper">
                        @if (!empty($transaction->unread_count))
                            <span class="product-badge">{{ $transaction->unread_count }}</span>
                        @endif
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}">
                        @endif
                    </div>
                    <p class="product-name">
                        <a href="{{ route('transactions.chat.show', $transaction) }}">
                            {{ $product->name }}
                        </a>
                    </p>
                </div>
            @empty
                <p class="empty-message">商品はありません</p>
            @endforelse
        @else
            @forelse ($items as $item)
                @php
                    $product = $item['product'] ?? null;
                    $transaction = $item['transaction'] ?? null;
                    $imageUrl = null;
                    if ($product && $product->image_path) {
                        $imagePath = $product->image_path;
                        if (preg_match('/^https?:\\/\\//', $imagePath)) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, '/storage/')) {
                            $imageUrl = asset(ltrim($imagePath, '/'));
                        } elseif (str_starts_with($imagePath, 'storage/')) {
                            $imageUrl = asset($imagePath);
                        } else {
                            $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
                        }
                    }
                @endphp
                <div class="product-card">
                    <div class="product-image-wrapper">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}">
                        @endif
                    </div>
                    <p class="product-name">
                        @if ($product && $transaction)
                            <a href="{{ route('transactions.chat.show', $transaction) }}">
                                {{ $product->name }}
                            </a>
                        @elseif ($product)
                            <a href="{{ route('products.show', ['itemId' => $product->id]) }}">
                                {{ $product->name }}
                            </a>
                        @else
                            商品名
                        @endif
                    </p>
                </div>
            @empty
                <p class="empty-message">商品はありません</p>
            @endforelse
        @endif
    </div>
</div>
@endsection
