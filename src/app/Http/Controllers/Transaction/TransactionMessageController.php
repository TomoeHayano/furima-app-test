<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\TransactionMessageRequest;
use App\Models\Transaction;
use App\Models\TransactionMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionMessageController extends Controller
{
    public function store(TransactionMessageRequest $request, Transaction $transaction): RedirectResponse
    {
        $userId = (int) Auth::id();
        abort_unless($this->isParticipant($transaction, $userId), 403);

        $imagePath = null;
        if ($request->hasFile('image')) {
            /** @var \Illuminate\Http\UploadedFile $file */
            $file = $request->file('image');
            $imagePath = $file->store('transaction_messages', 'public');
        }

        $transaction->messages()->create([
            'sender_id'  => $userId,
            'body'       => (string) $request->input('body'),
            'image_path' => $imagePath,
        ]);

        // FN009: 本文の入力保持（localStorage）クリア用
        return redirect()
            ->route('transactions.chat.show', $transaction)
            ->with('message_posted', true);
    }

    public function update(TransactionMessageRequest $request, Transaction $transaction, TransactionMessage $message): RedirectResponse
    {
        $userId = (int) Auth::id();
        abort_unless($this->isParticipant($transaction, $userId), 403);
        abort_unless((int) $message->transaction_id === (int) $transaction->id, 404);
        abort_unless((int) $message->sender_id === $userId, 403);

        // 編集は本文のみ（画像差し替え仕様が無い前提）
        $message->update([
            'body' => (string) $request->input('body'),
        ]);

        return redirect()->route('transactions.chat.show', $transaction);
    }

    public function destroy(Transaction $transaction, TransactionMessage $message): RedirectResponse
    {
        $userId = (int) Auth::id();
        abort_unless($this->isParticipant($transaction, $userId), 403);
        abort_unless((int) $message->transaction_id === (int) $transaction->id, 404);
        abort_unless((int) $message->sender_id === $userId, 403);

        if ($message->image_path !== null) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();

        return redirect()->route('transactions.chat.show', $transaction);
    }

    private function isParticipant(Transaction $transaction, int $userId): bool
    {
        return (int) $transaction->buyer_id === $userId || (int) $transaction->seller_id === $userId;
    }
}
