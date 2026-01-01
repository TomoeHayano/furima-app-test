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

    public function show(Transaction $transaction): View
    {
        $userId = (int) Auth::id();
        abort_unless($this->isParticipant($transaction, $userId), 403);

        $transaction->loadMissing([
            'order.product',
            'buyer.profile',
            'seller.profile',
        ]);

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

        $messages = $transaction->messages()
            ->with(['sender:id,name', 'sender.profile:id,user_id,image_path'])
            ->orderBy('created_at')
            ->get();

        $transaction->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->update(['read_at' => now()]);

        $isBuyer  = ((int) $transaction->buyer_id === $userId);
        $isSeller = ((int) $transaction->seller_id === $userId);

        $shouldShowBuyerComplete =
            $isBuyer
            && $transaction->buyer_completed_at === null
            && $transaction->completed_at === null;

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

        $transaction->loadMissing([
            'seller:id,email,name',
            'buyer:id,name',
            'order.product:id,name',
        ]);

        if ($transaction->seller && $transaction->seller->email) {
            Mail::to((string) $transaction->seller->email)
                ->send(new BuyerCompletedNotification($transaction));
        }

        $this->completeIfPossible($transaction);

        return redirect()->to('/mypage?page=progress');
    }

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

        return redirect()->to('/mypage?page=progress');
    }

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

    private function isParticipant(Transaction $transaction, int $userId): bool
    {
        return (int) $transaction->buyer_id === $userId
            || (int) $transaction->seller_id === $userId;
    }
}
