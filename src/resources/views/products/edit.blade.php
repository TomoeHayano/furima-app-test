@extends('layouts.app')

@section('title', '住所の変更')

@push('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endpush

@section('content')
<div class="address-edit-container">
    <h1 class="address-title">住所の変更</h1>

    {{-- 住所編集フォーム --}}
    <form action="{{ route('purchase.address.update', $product->id) }}" method="POST">
        @csrf

        <div class="form-group postal-code-group">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code"
                value="{{ old('postal_code') }}">
            @error('postal_code')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group address-group">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" id="address"
                value="{{ old('address') }}">
            @error('address')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="building_name" class="building-label">建物名</label>    
            <input type="text" name="building_name" id="building_name">
        </div>

        <button type="submit">更新する</button>
    </form>
</div>
@endsection