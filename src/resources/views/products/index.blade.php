@extends('layouts.app')

@section('title', '商品一覧')

@push('css')
<link rel="stylesheet" href="{{ asset('css/products.css') }}">
@endpush

@section('content')
<div class="products-page">

    @php
        $keywordQuery = request()->filled('keyword')
            ? ['keyword' => request('keyword')]
            : [];
    @endphp
    {{-- タブ --}}
    <div class="products-tabs">
        {{-- おすすめ --}}
        <a href="{{ $guest ? route('products.index', $keywordQuery) : route('products.mylist', array_merge(['tab' => 'recommend'], $keywordQuery)) }}"
            class="tab-link {{ $tab === 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>

        {{-- マイリスト（ログイン時のみ） --}}
        @unless($guest)
            <a href="{{ route('products.mylist', array_merge(['tab' => 'mylist'], $keywordQuery)) }}"
                class="tab-link {{ $tab === 'mylist' ? 'active' : '' }}">
                マイリスト
            </a>
        @endunless
    </div>

    <div class="products-border"></div>

    {{-- 商品一覧グリッド --}}
    <div class="products-grid">
        @forelse ($products as $product)
            <div class="product-card">
                <div class="product-image">
                    @if (!empty($product['image_path']))
                        <img src="{{ $product['image_path'] }}" alt="{{ $product['name'] }}">
                    @else
                        商品画像
                    @endif
                </div>
                <div class="product-name">
                    <a href="{{ route('products.show', ['itemId' => $product['id']]) }}">
                        {{ $product['name'] }}
                    </a>
                </div>
                @if ($product['is_sold'])
                    <span class="sold-badge">SOLD</span>
                @endif
            </div>
        @empty
            <p>商品がありません。</p>
        @endforelse
    </div>
</div>
@endsection
