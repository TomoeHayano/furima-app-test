@extends('layouts.app')

@section('title', 'プロフィール設定')

@push('css')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
@endpush

@section('content')
<div class="profile-edit-content">
    <h1 class="profile-title">プロフィール設定</h1>
    
     <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form" novalidate>
        @csrf
        
        <!-- プロフィール画像 -->
        <div class="profile-image-section">
            <div class="profile-image-circle">
                @if($user->profile && $user->profile->image_path)
                    <img src="{{ asset('storage/' . $user->profile->image_path) }}" alt="プロフィール画像" class="current-image">
                @endif
            </div>
            <label for="image_path" class="image-select-button">
                画像を選択する
                <input type="file" id="image_path" name="image_path" accept="image/jpeg,image/png" style="display: none;">
            </label>
            @if ($errors->has('image_path'))
                @foreach ($errors->get('image_path') as $message)
                    <p class="error-message">{{ $message }}</p>
                @endforeach
            @endif
        </div>

        <!-- ユーザー名 -->
        <div class="form-group">
            <label for="name" class="form-label">ユーザー名</label>
            <input type="text" id="name" name="name" class="form-input{{ $errors->has('name') ? ' is-error' : '' }}" value="{{ old('name', $user->name) }}" required>
            @if ($errors->has('name'))
                @foreach ($errors->get('name') as $message)
                    <p class="error-message">{{ $message }}</p>
                @endforeach
            @endif
        </div>

        <!-- 郵便番号 -->
        <div class="form-group">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" class="form-input{{ $errors->has('postal_code') ? ' is-error' : '' }}" value="{{ old('postal_code', $user->profile->postal_code ?? '') }}" placeholder="123-4567" required>
            @if ($errors->has('postal_code'))
                @foreach ($errors->get('postal_code') as $message)
                    <p class="error-message">{{ $message }}</p>
                @endforeach
            @endif
        </div>

        <!-- 住所 -->
        <div class="form-group">
            <label for="address" class="form-label">住所</label>
            <input type="text" id="address" name="address" class="form-input{{ $errors->has('address') ? ' is-error' : '' }}" value="{{ old('address', $user->profile->address ?? '') }}" required>
            @if ($errors->has('address'))
                @foreach ($errors->get('address') as $message)
                    <p class="error-message">{{ $message }}</p>
                @endforeach
            @endif
        </div>

        <!-- 建物名 -->
        <div class="form-group">
            <label for="building_name" class="form-label">建物名</label>
            <input type="text" id="building_name" name="building_name" class="form-input" value="{{ old('building_name', $user->profile->building_name ?? '') }}">
        </div>

        <!-- 更新ボタン -->
        <div class="action-bar">
            <button type="submit" class="update-button">更新する</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image_path');
    const imageCircle = document.querySelector('.profile-image-circle');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let img = imageCircle.querySelector('.current-image');
                if (!img) {
                    img = document.createElement('img');
                    img.className = 'current-image';
                    img.alt = 'プロフィール画像';
                    imageCircle.appendChild(img);
                }
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
