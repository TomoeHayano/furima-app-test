@extends('layouts.app')

@section('title', '商品購入画面')

@push('css')
<link rel="stylesheet" href="{{ asset('css/order.css') }}">
@endpush

@section('content')
<div class="purchase-container">

    {{-- 商品画像・商品名・価格 --}}
    <div class="product-info">
        <div class="product-image">
            <img src="{{ $product->image_path }}" alt="商品画像">
        </div>
        <div class="product-text">
            <p class="product-name">{{ $product->name }}</p>
            <p class="product-price">¥{{ number_format($product->price) }}</p>
        </div>
    </div>

    {{-- 下線 --}}
    <hr class="line product-border single-line">

    {{-- 購入フォーム --}}
    <form action="{{ route('purchase.store', $product->id) }}" method="POST">
        @csrf
        @php
            $useSessionShipping = is_array($shippingAddress);
            $shippingPostalCode = $useSessionShipping
                ? data_get($shippingAddress, 'postal_code', '')
                : optional($user->profile)->postal_code;
            $shippingAddressLine = $useSessionShipping
                ? data_get($shippingAddress, 'address', '')
                : optional($user->profile)->address;
            $shippingBuilding = $useSessionShipping
                ? data_get($shippingAddress, 'building_name', '')
                : optional($user->profile)->building_name;
            $formattedAddressParts = [];
            if ($shippingPostalCode) {
                $formattedAddressParts[] = '〒' . $shippingPostalCode;
            }
            if ($shippingAddressLine) {
                $formattedAddressParts[] = $shippingAddressLine;
            }
            if ($shippingBuilding) {
                $formattedAddressParts[] = $shippingBuilding;
            }
            $formattedAddress = implode(' ', $formattedAddressParts);
        @endphp

        {{-- 支払い方法 --}}
        <div class="payment-section">
            <div class="payment-label-wrapper">
                <label for="payment_method" class="payment-label">支払い方法</label>
                <select id="payment_method" name="payment_method" class="status-dropdown">
                    <option value="" disabled hidden selected>選択してください</option>
                    <option value="コンビニ支払い">コンビニ支払い</option>
                    <option value="カード支払い">カード支払い</option>
                </select>
            </div>
        </div>

        @error('payment_method')
            <p class="form-error payment-error">{{ $message }}</p>
        @enderror
        {{-- 下線 --}}
        <hr class="line payment-border single-line">

        {{-- 配送先 --}}
        <div class="address-section">
            <span class="address-label">配送先</span>
            @if($shippingPostalCode || $shippingAddressLine)
                <span class="address-text">
                    @if($shippingPostalCode)
                        〒{{ $shippingPostalCode }}<br><br>
                    @endif
                    {{ $shippingAddressLine }}
                    @if(!empty($shippingBuilding))
                        <br><br>{{ $shippingBuilding }}
                    @endif
                </span>
            @else
                <span class="address-text">住所が登録されていません</span>
            @endif
            <a href="{{ route('purchase.address.edit', $product->id) }}" class="address-edit">変更する</a>
        </div>

        <input type="hidden" name="address" value="{{ $shippingPostalCode || $shippingAddressLine ? $formattedAddress : '' }}">

        @error('address')
            <p class="form-error address-error">{{ $message }}</p>
        @enderror
        {{-- 下線 --}}
        <hr class="line address-border single-line">

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

            {{-- 購入ボタン --}}
            <button type="submit" class="purchase-button">購入する</button>
        </div>
    </form>
</div>

<script>
    const dropdown = document.getElementById('payment_method');
    const selectedMethod = document.getElementById('selected-method');

    dropdown.addEventListener('change', function () {
        selectedMethod.textContent = this.value || '---';
    });
</script>
@endsection
