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
        <a href="{{ $guest ? url('/') : url('/mylist?tab=recommend') }}"
            class="tab-link {{ $tab === 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>

        {{-- マイリスト（ログイン時のみ） --}}
        @unless($guest)
            <a href="{{ url('/mylist?tab=mylist') }}{{ request('keyword') ? '&keyword='.request('keyword') : '' }}"
                class="tab-link {{ $tab === 'mylist' ? 'active' : '' }}">
                マイリスト
            </a>
        @endunless
    </div>

    <div class="products-border"></div>

    {{-- 商品一覧グリッド --}}
    <div class="products-grid">
        @forelse ($products as $product)
            <div class="product-card"> {{-- ✅ products-card → product-card --}}
                <div class="product-image">
                    商品画像
                </div>
                <div class="product-name"> {{-- ✅ products-name → product-name --}}
                    <a href="{{ route('products.show', ['item_id' => $product['id']]) }}">
                        {{ $product['name'] }}
                    </a>
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
