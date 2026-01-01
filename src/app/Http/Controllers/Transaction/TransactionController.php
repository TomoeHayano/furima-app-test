<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Mail\BuyerCompletedNotification;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    /**
     * 取引チャット画面
     */
    public function show(Transaction $transaction): View
    {
        $userId = (int) Auth::id();
        abort_unless($this->isParticipant($transaction, $userId), 403);

        // 画像を使う関連を先読み
        $transaction->loadMissing([
            'order.product',
            'buyer.profile',
            'seller.profile',
        ]);

        /**
         * サイドバー（取引中一覧）
         * ・未完了 + 完了後7日以内
         * ・自分が購入者 or 出品者
         * ・最新メッセージ順
         */
        $sidebarTransactions = Transaction::query()
            ->whereNull('completed_at')
            ->where(static function (Builder $query) use ($userId): void {
                $query->where('buyer_id', $userId)
                      ->orWhere('seller_id', $userId);
            })
            ->with(['order.product', 'buyer:id,name', 'seller:id,name'])
            ->withCount([
                'messages as unread_count' => static function (Builder $query) use ($userId): void {
                    $query->whereNull('read_at')
                          ->where('sender_id', '!=', $userId);
                },
            ])
            ->withMax('messages as latest_message_created_at', 'created_at')
            ->orderByDesc('latest_message_created_at')
            ->orderByDesc('updated_at')
            ->get();

        /**
         * メッセージ一覧
         */
        $messages = $transaction->messages()
            ->with(['sender:id,name', 'sender.profile:id,user_id,image_path'])
            ->orderBy('created_at')
            ->get();

        /**
         * 未読を既読化
         */
        $transaction->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->update(['read_at' => now()]);

        $isBuyer  = ((int) $transaction->buyer_id === $userId);
        $isSeller = ((int) $transaction->seller_id === $userId);

        /**
         * 購入者：まだ完了していない場合のみ「取引完了（評価）」ボタンを表示
         */
        $shouldShowBuyerComplete =
            $isBuyer
            && $transaction->buyer_completed_at === null
            && $transaction->completed_at === null;

        /**
         * 出品者：購入者完了後、まだ自分が完了していない場合のみ評価ダイアログ表示
         */
        $shouldShowSellerRating =
            $isSeller
            && $transaction->buyer_completed_at !== null
            && $transaction->seller_completed_at === null
            && $transaction->completed_at === null;

        $isFullyCompleted =
            $transaction->completed_at !== null
            || ($transaction->buyer_rating !== null && $transaction->seller_rating !== null);

        return view('transactions.show', compact(
            'transaction',
            'sidebarTransactions',
            'messages',
            'isBuyer',
            'isSeller',
            'shouldShowBuyerComplete',
            'shouldShowSellerRating',
            'isFullyCompleted',
        ));
    }

    /**
     * 購入者：評価送信
     * → 送信後は必ずマイページへ遷移
     */
    public function completeByBuyer(Request $request, Transaction $transaction): RedirectResponse
    {
        $userId = (int) Auth::id();
        abort_unless((int) $transaction->buyer_id === $userId, 403);
        abort_unless($transaction->completed_at === null, 400);

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $transaction->buyer_rating        = (int) $request->input('rating');
        $transaction->buyer_rated_at      = now();
        $transaction->buyer_completed_at  = now();
        $transaction->save();

        /**
         * 出品者へメール通知
         */
        $transaction->loadMissing([
            'seller:id,email,name',
            'buyer:id,name',
            'order.product:id,name',
        ]);

        if ($transaction->seller && $transaction->seller->email) {
            Mail::to((string) $transaction->seller->email)
                ->send(new BuyerCompletedNotification($transaction));
        }

        /**
         * 双方完了していれば completed_at を立てる
         * ※ 画面遷移とは無関係（DB状態管理用）
         */
        $this->completeIfPossible($transaction);

        // ✅ 評価送信後は必ずマイページへ
        return redirect()->to('/mypage?page=progress');
    }

    /**
     * 出品者：評価送信
     * → 送信後は必ずマイページへ遷移
     */
    public function completeBySeller(Request $request, Transaction $transaction): RedirectResponse
    {
        $userId = (int) Auth::id();
        abort_unless((int) $transaction->seller_id === $userId, 403);
        abort_unless($transaction->completed_at === null, 400);
        abort_unless($transaction->buyer_completed_at !== null, 400);

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $transaction->seller_rating        = (int) $request->input('rating');
        $transaction->seller_rated_at      = now();
        $transaction->seller_completed_at  = now();
        $transaction->save();

        $this->completeIfPossible($transaction);

        // ✅ 評価送信後は必ずマイページへ
        return redirect()->to('/mypage?page=progress');
    }

    /**
     * 双方が評価・完了していれば取引完了とする
     */
    private function completeIfPossible(Transaction $transaction): void
    {
        if (
            $transaction->buyer_completed_at !== null &&
            $transaction->seller_completed_at !== null &&
            $transaction->buyer_rating !== null &&
            $transaction->seller_rating !== null &&
            $transaction->completed_at === null
        ) {
            $transaction->completed_at = now();
            $transaction->save();
        }
    }

    /**
     * 自分が取引当事者かどうか
     */
    private function isParticipant(Transaction $transaction, int $userId): bool
    {
        return (int) $transaction->buyer_id === $userId
            || (int) $transaction->seller_id === $userId;
    }
}
