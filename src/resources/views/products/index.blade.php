@extends('layouts.app')

@section('title', '商品一覧')

@push('css')
<link rel="stylesheet" href="{{ asset('css/products.css') }}">
@endpush

@section('content')
<div class="products-page">
    {{-- タブ --}}
    <div class="products-tabs">
        {{-- おすすめ --}}
        <a href="{{ url('/') }}"
        class="tab-link {{ request('tab') !== 'mylist' ? 'active' : '' }}">
        おすすめ
        </a>

        {{-- マイリスト（ログイン時のみ表示） --}}
        @auth
            <a href="{{ url('/?tab=mylist') }}"
            class="tab-link {{ request('tab') === 'mylist' ? 'active' : '' }}">
            マイリスト
            </a>
        @endauth
    </div>

    <div class="products-border"></div>

    {{-- 商品一覧グリッド --}}
    <div class="products-grid">
        @forelse ($products as $product)
            <div class="product-card">
                <div class="product-image">
                    商品画像
                </div>
                <div class="product-name">
                    {{ $product['name'] }}
                </div>
                @if ($product['is_sold'])
                    <div class="sold-label">Sold</div>
                @endif
            </div>
        @empty
            <p>商品がありません。</p>
        @endforelse
    </div>
</div>
@endsection
