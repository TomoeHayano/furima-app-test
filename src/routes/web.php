<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\VerificationController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 会員登録 → プロフィール画面までのルート定義
|
*/

// 会員登録
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// メール認証
Route::get('/email/verify', [VerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/prompt', [VerificationController::class, 'prompt'])
    ->middleware('auth')
    ->name('verification.prompt');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

// ログイン
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// 商品一覧（トップ画面）
Route::get('/', [ProductController::class, 'index'])->name('products.index');

// 商品詳細
Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('products.show');

// いいね機能
Route::post('/item/{productId}/like', [ProductController::class, 'toggleLike'])->name('products.toggleLike')
    ->middleware('auth')
    ->name('products.toggleLike');

// 認証後
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 新規登録後に使う（初回なら編集画面 → 登録済なら商品一覧へ）
    Route::get('/mypage', [UserController::class, 'show'])->name('user.mypage');

    // プロフィール編集
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('user.profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('user.profile.update');
    
    //商品一覧マイリスト
    Route::get('/mylist', [ProductController::class, 'mylist'])->name('products.mylist');
    
    //いいね機能
    Route::post('/item/{productId}/like', [ProductController::class, 'toggleLike'])
        ->name('products.toggleLike');

    //コメント機能
    Route::post('/item/{productId}/comment', [ProductController::class, 'storeComment'])
    ->middleware('auth')
    ->name('products.comment.store');

    // 住所編集
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('purchase.address.update');
    
    // 購入処理
    Route::get('/purchase/{item_id}', [OrderController::class, 'create'])->name('purchase.create');
Route::post('/purchase/{item_id}', [OrderController::class, 'store'])->name('purchase.store');

    // Stripeコールバック
    Route::get('/purchase/{item_id}/success', [OrderController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/cancel', [OrderController::class, 'cancel'])->name('purchase.cancel');
    
    // 商品出品画面
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');

    // 商品出品処理
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
});
    
