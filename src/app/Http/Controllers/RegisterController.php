<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * 新しいユーザーを登録する。
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        // RegisterRequestが自動的にバリデーションを実行し、
        // 失敗した場合はエラーメッセージと共に元のフォームにリダイレクトします。
        // バリデーションが成功した場合のみ、以下の処理が実行されます。

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ユーザー登録後のリダイレクト先を適切に設定してください
        // 例：return redirect('/dashboard');

        return redirect('/')->with('success', '登録が完了しました。');
    }
}
