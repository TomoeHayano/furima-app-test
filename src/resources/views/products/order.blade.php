@extends('layouts.app')

@section('title', '商品購入画面')

@push('css')
<link rel="stylesheet" href="{{ asset('css/order.css') }}">
@endpush

@section('content')
<div class="purchase-container">

    {{-- 左側 商品情報 --}}
    <div class="purchase-left">
        <div class="product-image">
            <img src="{{ $product->image_path }}" alt="商品画像">
        </div>
        <div class="product-info">
            <h1>{{ $product->name }}</h1>
            <p class="price">¥{{ number_format($product->price) }}</p>
        </div>

        {{-- 支払い方法 --}}
        <form action="{{ route('purchase.store', $product->id) }}" method="POST">
            @csrf

            {{-- 支払い方法 --}}
            <label for="payment_method">支払い方法</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">選択してください</option>
                <option value="コンビニ支払い">コンビニ支払い</option>
                <option value="カード支払い">カード支払い</option>
            </select>
            @error('payment_method')
                <p class="form-error">{{ $message }}</p>
            @enderror

            {{-- 配送先 --}}
            <label for="address">配送先</label>
            <input type="text" name="address" id="address" value="{{ old('address', $profile->address ?? '') }}" required>
            @error('address')
                <p class="form-error">{{ $message }}</p>
            @enderror

            <button type="submit" class="purchase-button">購入する</button>
        </form>
    </div>

    {{-- 右側 確認エリア --}}
    <div class="purchase-right">
        <div class="confirm-surface">
            <div class="confirm-row">
                <span>商品代金</span>
                <span>¥{{ number_format($product->price) }}</span>
            </div>
            <div class="confirm-row">
                <span>支払い方法</span>
                <span id="selected-method">---</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('payment_method').addEventListener('change', function() {
        document.getElementById('selected-method').textContent = this.value || '---';
    });
</script>
@endsection