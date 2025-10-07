@extends('layouts.app')

@section('title', '住所の変更')

@push('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endpush

@section('content')
<div class="address-edit-container">
    <h1 class="address-title">住所の変更</h1>

    {{-- 住所編集フォーム --}}
    <form action="{{ route('purchase.address.update', $product->id) }}" method="POST" class="address-form">
        @csrf

        <div class="form-group postal-code-group">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="form-input"
                value="{{ old('postal_code', $profile->postal_code ?? '') }}" placeholder="123-4567">
            @error('postal_code')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group address-group">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" id="address" class="form-input"
                value="{{ old('address', $profile->address ?? '') }}" placeholder="東京都渋谷区渋谷1-2-3">
            @error('address')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group building-group">
            <label for="building_name" class="form-label">建物名</label>
            <input type="text" name="building_name" id="building_name" class="form-input"
                value="{{ old('building_name', $profile->building_name ?? '') }}" placeholder="サンプルビル101号室">
            @error('building_name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-button">更新する</button>
        </div>
    </form>
</div>
@endsection
