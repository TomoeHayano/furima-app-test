<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\BuyerCompleteRequest;
use App\Http\Requests\Transaction\SellerCompleteRequest;
use App\Mail\TransactionBuyerCompletedMail;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function show(Transaction $transaction): View
    {
        $userId = (int) Auth::id();
        abort_unless($this->isParticipant($transaction, $userId), 403);

        // サイドバー（取引中一覧）：新着メッセージ順 + 未読件数
        $sidebarTransactions = Transaction::query()
            ->whereNull('completed_at')
            ->where(static function (Builder $query) use ($userId): void {
                $query->where('buyer_id', $userId)->orWhere('seller_id', $userId);
            })
            ->with(['order.product', 'buyer:id,name', 'seller:id,name'])
            ->withCount(['messages as unread_count' => static function (Builder $query) use ($userId): void {
                $query->whereNull('read_at')->where('sender_id', '!=', $userId);
            }])
            ->withMax('messages as latest_message_created_at', 'created_at')
            ->orderByDesc('latest_message_created_at')
            ->orderByDesc('updated_at')
            ->get();

        // メッセージ一覧
        $messages = $transaction->messages()
            ->with('sender:id,name')
            ->orderBy('created_at')
            ->get();

        // 相手からの未読を既読化
        $transaction->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->update(['read_at' => now()]);

        $isBuyer = ((int) $transaction->buyer_id === $userId);
        $isSeller = ((int) $transaction->seller_id === $userId);

        // 購入者：まだ完了していないなら「完了ボタン（評価必須）」を出す
        $shouldShowBuyerComplete = $isBuyer
            && $transaction->buyer_completed_at === null
            && $transaction->completed_at === null;

        // 出品者：購入者が完了済みで、自分がまだ完了してないなら評価モーダルを出す
        $shouldShowSellerRating = $isSeller
            && $transaction->buyer_completed_at !== null
            && $transaction->seller_completed_at === null
            && $transaction->completed_at === null;

        return view('transactions.chat.show', compact(
            'transaction',
            'sidebarTransactions',
            'messages',
            'isBuyer',
            'isSeller',
            'shouldShowBuyerComplete',
            'shouldShowSellerRating',
        ));
    }

    public function completeByBuyer(BuyerCompleteRequest $request, Transaction $transaction): RedirectResponse
    {
        $userId = (int) Auth::id();
        abort_unless((int) $transaction->buyer_id === $userId, 403);
        abort_unless($transaction->completed_at === null, 400);

        $transaction->buyer_rating = (int) $request->input('rating');
        $transaction->buyer_rated_at = now();
        $transaction->buyer_completed_at = now();
        $transaction->save();

        // 出品者へメール通知（Mailhog/Mailtrap）
        $transaction->loadMissing('seller:id,email');
        Mail::to((string) $transaction->seller->email)->send(new TransactionBuyerCompletedMail($transaction));

        $this->completeIfPossible($transaction);

        // FN014：送信後はトップへ
        return redirect()->to('/');
    }

    public function completeBySeller(SellerCompleteRequest $request, Transaction $transaction): RedirectResponse
    {
        $userId = (int) Auth::id();
        abort_unless((int) $transaction->seller_id === $userId, 403);
        abort_unless($transaction->completed_at === null, 400);
        abort_unless($transaction->buyer_completed_at !== null, 400); // 購入者完了後のみ

        $transaction->seller_rating = (int) $request->input('rating');
        $transaction->seller_rated_at = now();
        $transaction->seller_completed_at = now();
        $transaction->save();

        $this->completeIfPossible($transaction);

        // FN014：送信後はトップへ
        return redirect()->to('/');
    }

    private function completeIfPossible(Transaction $transaction): void
    {
        if (
            $transaction->buyer_completed_at !== null
            && $transaction->seller_completed_at !== null
            && $transaction->buyer_rating !== null
            && $transaction->seller_rating !== null
            && $transaction->completed_at === null
        ) {
            $transaction->completed_at = now();
            $transaction->save();
        }
    }

    private function isParticipant(Transaction $transaction, int $userId): bool
    {
        return (int) $transaction->buyer_id === $userId || (int) $transaction->seller_id === $userId;
    }
}
