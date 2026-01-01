<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuyerCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction;

    /**
     * Create a new message instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $productName = optional($this->transaction->order)->product->name ?? '商品';
        $buyerName   = optional($this->transaction->buyer)->name ?? '購入者';
        $rating      = $this->transaction->buyer_rating;

        return $this->subject('フリマアプリで取引が完了しました')
            ->view('emails.transactions.buyer-completed')
            ->with([
                'productName' => $productName,
                'buyerName'   => $buyerName,
                'rating'      => $rating,
            ]);
    }
}
