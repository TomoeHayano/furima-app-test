<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 会員登録 → プロフィール画面までのルート定義
|
*/

// 商品一覧（トップ画面）
Route::get('/', [ProductController::class, 'index'])->name('products.index');

// 商品詳細
Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('products.show');

// いいね機能
Route::post('/item/{productId}/like', [ProductController::class, 'toggleLike'])->name('products.toggleLike')
    ->middleware('auth')
    ->name('products.toggleLike');

// 会員登録
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ログイン
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// 認証後
Route::middleware('auth')->group(function () {
    // プロフィール画面（初回は設定フォーム、登録済みなら一覧＋編集ボタン）
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
});
    