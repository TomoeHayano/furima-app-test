<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  public function showLoginForm()
  {
    return view('auth.login');
  }

  public function login(LoginRequest $request)
  {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
      if (! Auth::user()->hasVerifiedEmail()) {
        // 未認証ユーザーは誘導画面へ
        return redirect()->route('verification.notice');
      }

      // ログイン成功 → 商品管理画面へ
      return redirect()->route('products.mylist', ['tab' => 'mylist']);
    }

    return back()->withErrors([
      'email' => 'ログイン情報が登録されていません',
    ]);
  }
}
