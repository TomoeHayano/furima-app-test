<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;
use App\Models\User;

class UserController extends Controller
{
    // マイページ（新規登録後の初回設定用）
    public function show(Request $request)
    {
        $user = Auth::user();

        // プロフィール未登録 → 編集画面を表示
        if (!$user->profile || !$user->profile->address) {
            return view('mypage.profile_edit', compact('user'));
        }

        // プロフィール登録済み → 商品一覧へリダイレクト
        return redirect()->route('products.index');
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

        // ユーザー名を更新
        $user->update([
            'name' => $request->name,
        ]);

        $profileData = [
            'postal_code'   => $request->postal_code,
            'address'       => $request->address,
            'building_name' => $request->building_name,
        ];

        // 画像アップロード
        if ($request->hasFile('image_path')) {
            if ($user->profile && $user->profile->image_path) {
                Storage::disk('public')->delete($user->profile->image_path);
            }

            $imagePath = $request->file('image_path')->store('profile_images', 'public');
            $profileData['image_path'] = $imagePath;
        }

        // プロフィール更新 or 新規作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('products.index')->with('success', 'プロフィールを更新しました！');
    }
}