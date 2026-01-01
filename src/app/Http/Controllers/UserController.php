<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show(Request $request)
    {   
        /** @var User $user */
        $user = Auth::user();

        $items = collect();
        if (! $user->profile || ! $user->profile->address) {
            return redirect()->route('user.profile.edit');
        }

        $page     = $request->query('page', 'sell');
        $transactions = collect();
        $ratingAverage = null;

        $sellerCount = (int) Transaction::query()
            ->where('seller_id', $user->id)
            ->whereNotNull('buyer_rating')
            ->count();

        $sellerSum = (int) Transaction::query()
            ->where('seller_id', $user->id)
            ->whereNotNull('buyer_rating')
            ->sum('buyer_rating');

        $buyerCount = (int) Transaction::query()
            ->where('buyer_id', $user->id)
            ->whereNotNull('seller_rating')
            ->count();

        $buyerSum = (int) Transaction::query()
            ->where('buyer_id', $user->id)
            ->whereNotNull('seller_rating')
            ->sum('seller_rating');

        $totalCount = $sellerCount + $buyerCount;
        $totalSum   = $sellerSum + $buyerSum;

        if ($totalCount > 0) {
            $ratingAverage = (int) round($totalSum / $totalCount); // 四捨五入（整数）
        }

        $baseProgressQuery = Transaction::query()
            ->whereNull('completed_at')
            ->where(static function (Builder $query) use ($user): void {
                $query->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            });

        $progressUnreadTotal = (int) (clone $baseProgressQuery)
            ->withCount(['messages as unread_count' => static function (Builder $query) use ($user): void {
                $query->whereNull('read_at')
                    ->where('sender_id', '!=', $user->id);
            }])
            ->get()
            ->sum('unread_count');

        if ($page === 'buy') {
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

            $items = $completedBuyerTransactions
                ->map(static function (Transaction $transaction) {
                    return [
                        'product'     => optional($transaction->order)->product,
                        'transaction' => $transaction,
                    ];
                })
                ->filter(static fn ($item) => $item['product'] !== null);
        } elseif ($page === 'progress') {

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

            $sellProducts = $user->products()
                ->with('order')
                ->orderByDesc('created_at')
                ->get();

            $orderIds = $sellProducts
                ->pluck('order.id')
                ->filter()
                ->unique()
                ->values();

            $transactionsByOrderId = Transaction::query()
                ->whereIn('order_id', $orderIds)
                ->get()
                ->keyBy('order_id');

            $items = $sellProducts->map(function ($product) use ($transactionsByOrderId) {
                $orderId     = optional($product->order)->id;
                $transaction = $orderId ? $transactionsByOrderId->get($orderId) : null;

                return [
                    'product'     => $product,
                    'transaction' => $transaction,
                ];
            });
        }

        return view('mypage.profile', compact(
            'user',
            'items',
            'page',
            'transactions',
            'progressUnreadTotal',
            'ratingAverage'
        ));
    }

    public function edit(Request $request)
    {
        $user = $request->user();

        return view('mypage.profile_edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        $user->update([
            'name' => $request->name,
        ]);

        $profileData = [
            'postal_code'   => $request->postal_code,
            'address'       => $request->address,
            'building_name' => $request->building_name,
        ];

        if ($request->hasFile('image_path')) {

            $oldImagePath = optional($user->profile)->image_path;

            $imagePath                 = $request->file('image_path')->store('profile_images', 'public');
            $profileData['image_path'] = $imagePath;

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            if ($oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return redirect()->route('products.mylist', ['tab' => 'mylist'])->with('success', 'プロフィールを更新しました！');
    }
}
