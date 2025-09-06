<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileUpdateRequest; // Form Requestをインポート
use App\Models\User;
use App\Models\Profile;

class UserController extends Controller
{
    // プロフィール画面
    public function show(Request $request)
    {
        $user = $request->user();

        // プロフィール未登録なら編集フォームをそのまま表示
        if (!$user->profile || !$user->profile->address) {
            return view('mypage.profile_edit', compact('user'));
        }

        // プロフィール登録済みなら、商品一覧を表示
        return view('mypage.show', compact('user'));
    }

    // プロフィール編集フォーム
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('mypage.profile_edit', compact('user'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['postal_code', 'address', 'building_name', 'image_path'])
        );

        return redirect()->route('user.mypage');
    }
}