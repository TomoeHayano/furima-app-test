@extends('layouts.app')

@section('title', '商品の出品')

@push('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endpush

@section('content')
<div class="sell">
    <h1 class="sell-title">商品の出品</h1>
    
    {{-- 出品フォーム --}}
    <form action="{{ route('sell.store') }}" method="post" enctype="multipart/form-data">
        @csrf
    
        {{-- 商品画像 --}}
        <div class="sell__section">
            <label for="image">商品画像</label>
                <div class="sell__image-upload" style="width:680px; height:150px; border:1px dashed #5F5F5F; border-radius:4px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png" style="display:none;">
                    <label for="image" class="sell__image-button" style="cursor:pointer; color:#FF5555; font-weight:700;">画像を選択する</label>
                    
                    {{-- プレビュー表示（枠の中に表示される） --}}
                    <img id="preview-image" src="" alt="プレビュー画像" style="max-width:100%; max-height:100%; display:none; object-fit:contain;">
                </div>

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
            <div class="sell__price-wrapper">
                <span class="sell__price-symbol">¥</span>
                <input type="number" name="price" id="price" value="{{ old('price') }}" placeholder="3000">
            </div>
            @error('price')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        
        <button type="submit" class="sell__button">出品する</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('image');
    if (!input) return;
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview-image');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });
});
</script>
@endsection
