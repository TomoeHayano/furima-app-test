<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileUpdateRequest; // Form Requestをインポート
use App\Models\User;
use App\Models\Profile;

class UserController extends Controller
{
    /**
     * プロフィール画面（マイページ）を表示する
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'default');
        $products = collect();

        if ($page === 'buy') {
            $products = $user->orders()->with('product')->get()->pluck('product');
        } elseif ($page === 'sell') {
            $products = $user->products()->get();
        }

        return view('user.mypage', compact('user', 'products', 'page'));
    }

    /**
     * プロフィール設定・編集画面を表示する
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new Profile(); // プロフィールがなければ新規作成
        
        return view('user.profile_edit', compact('user', 'profile'));
    }

    /**
     * プロフィール情報を更新する
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();

        // ユーザーテーブルの情報を更新
        $user->name = $request->input('name');
        $user->save();

        // プロフィールテーブルの情報を更新または新規作成
        $profile = $user->profile()->firstOrNew(['user_id' => $user->id]);
        $profile->fill($request->validated());
        $profile->save();

        // 完了後、指定されたルートにリダイレクト
        return redirect()->route('product.mylist')->with('success', 'プロフィールを更新しました。');
    }
}