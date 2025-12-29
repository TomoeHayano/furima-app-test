<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // マイページ（新規登録後の初回設定用）
    public function show(Request $request)
    {   
        /** @var User $user */
        $user = Auth::user();

        // プロフィール未登録 → 編集画面を表示
        if (! $user->profile || ! $user->profile->address) {
            return redirect()->route('user.profile.edit');
        }

        // 出品 / 購入 タブ切替
        $page     = $request->query('page', 'sell');
        $transactions = collect();

        $baseProgressQuery = Transaction::query()
            ->whereNull('completed_at')
            ->where(static function (Builder $query) use ($user): void {
                $query->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            });

        // 取引中未読合計はタブを開いていなくても表示する
        $progressUnreadTotal = (int) (clone $baseProgressQuery)
            ->withCount(['messages as unread_count' => static function (Builder $query) use ($user): void {
                $query->whereNull('read_at')
                    ->where('sender_id', '!=', $user->id);
            }])
            ->get()
            ->sum('unread_count');

        if ($page === 'buy') {
            // 購入者として取引完了した商品のみ
            $completedBuyerTransactions = Transaction::query()
                ->where('buyer_id', $user->id)
                ->whereNotNull('completed_at')
                ->with(['order.product'])
                ->orderByDesc('completed_at')
                ->get()
                ->filter(static function (Transaction $transaction): bool {
                    return $transaction->order !== null && $transaction->order->product !== null;
                })
                ->values();

            $products = $completedBuyerTransactions
                ->map(static fn (Transaction $transaction) => optional($transaction->order)->product)
                ->filter();
        } elseif ($page === 'progress') {
            // 取引中（購入者・出品者両方）を表示
            $transactions = (clone $baseProgressQuery)
                ->with(['order.product'])
                ->withCount(['messages as unread_count' => static function (Builder $query) use ($user): void {
                    $query->whereNull('read_at')
                        ->where('sender_id', '!=', $user->id);
                }])
                ->orderByDesc('updated_at')
                ->get()
                ->filter(static function (Transaction $transaction): bool {
                    return $transaction->order !== null && $transaction->order->product !== null;
                })
                ->values();

            $progressUnreadTotal = (int) $transactions->sum('unread_count');

            $products = $transactions
                ->map(static fn (Transaction $transaction) => optional($transaction->order)->product)
                ->filter();
        } else {
            $products = $user->products;
        }

        return view('mypage.profile', compact(
            'user',
            'products',
            'page',
            'transactions',
            'progressUnreadTotal'
        ));
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
            // 既存の画像パスを保存（削除前に取得）
            $oldImagePath = optional($user->profile)->image_path;

            // 新しい画像を保存
            $imagePath                 = $request->file('image_path')->store('profile_images', 'public');
            $profileData['image_path'] = $imagePath;

            // プロフィールデータを更新または作成
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            // 新しい画像の保存が成功した後に既存画像を削除
            if ($oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            // 画像がアップロードされなかった場合は通常の更新
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return redirect()->route('products.mylist', ['tab' => 'mylist'])->with('success', 'プロフィールを更新しました！');
    }
}
