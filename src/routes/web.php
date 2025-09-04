<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 認証不要なルート
Route::get('/', [ProductController::class, 'index'])->name('product.index');
Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('product.show');

// Fortifyの認証ルート
Route::middleware(['guest'])->group(function () {
    // 会員登録
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    // ログイン
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// 認証必須なルート
Route::middleware('auth')->group(function () {
    // ProductController
    Route::get('/?tab=mylist', [ProductController::class, 'mylist'])->name('product.mylist');
    Route::get('/sell', [ProductController::class, 'create'])->name('product.create');
    Route::post('/sell', [ProductController::class, 'store'])->name('product.store');

    // OrderController
    Route::get('/purchase/{item_id}', [OrderController::class, 'create'])->name('order.create');
    Route::post('/purchase/{item_id}', [OrderController::class, 'store'])->name('order.store');
    Route::get('/purchase/address/{item_id}', [OrderController::class, 'editAddress'])->name('order.edit_address');
    Route::post('/purchase/address/{item_id}', [OrderController::class, 'updateAddress'])->name('order.update_address');

    // UserController（プロフィール関連）
    Route::get('/mypage', [UserController::class, 'show'])->name('user.mypage');
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('user.profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('user.profile.update');
});