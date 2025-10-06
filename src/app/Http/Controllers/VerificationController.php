<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    // 認証誘導画面表示
    public function notice()
    {
        return view('auth.verify-email', ['mode' => 'guide']);
    }

    // 認証画面表示
    public function prompt()
    {
        return view('auth.verify-email', ['mode' => 'prompt']);
    }

    // 認証完了処理
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill(); // メール確認完了
        return redirect('/mypage/profile')->with('success', 'メール認証が完了しました！');
    }

    // 認証メール再送
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', '認証メールを再送しました。');
    }
}
