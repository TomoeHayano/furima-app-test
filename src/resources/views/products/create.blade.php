@extends('layouts.app')

@section('title', '商品の出品')

@push('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endpush

@section('content')
<div class="sell">
    
    <form action="{{ route('sell.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <div class="sell__section">
            <label for="image">商品画像</label>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png">
            @error('image')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品の詳細 --}}
        <div class="sell__section">
            <h2>商品の詳細</h2>

            {{-- カテゴリー --}}
            <label>カテゴリー</label>
            <div class="sell__categories">
                @foreach($categories as $category)
                    <label>
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}">
                        <span>{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('category_ids')
                <p class="error">{{ $message }}</p>
            @enderror

            {{-- 商品の状態 --}}
            <label for="condition_id">商品の状態</label>
            <select name="condition_id" id="condition_id">
                <option value="">選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}">{{ $condition->status_name }}</option>
                @endforeach
            </select>
            @error('condition_id')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品名と説明 --}}
        <div class="sell__section">
            <h2>商品名と説明</h2>

            <label for="name">商品名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="brand_name">ブランド名</label>
            <input type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}">

            <label for="description">商品の説明</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
            @error('description')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 価格 --}}
        <div class="sell__section">
            <label for="price">販売価格</label>
            <input type="number" name="price" id="price" min="0" value="{{ old('price') }}">
            @error('price')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="sell__button">出品する</button>
    </form>
</div>
@endsection