<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();

            // 1注文 = 1取引（取引ルーム）
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->unique('order_id', 'uq_transactions_order_id');

            // 参加者（購入者/出品者）
            $table->foreignId('buyer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('seller_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 相互評価（DB上は入力タイミングが後なので nullable）
            // 必須制御は「完了ボタン押下時」にFormRequestで担保する想定
            $table->unsignedTinyInteger('buyer_rating')->nullable();  // buyer -> seller (1..5)
            $table->unsignedTinyInteger('seller_rating')->nullable(); // seller -> buyer (1..5)
            $table->timestamp('buyer_rated_at')->nullable();
            $table->timestamp('seller_rated_at')->nullable();

            // 取引完了（両者が完了したら completed_at を埋める）
            $table->timestamp('buyer_completed_at')->nullable();
            $table->timestamp('seller_completed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // 取引中タブ: buyer_id or seller_id + completed_at null でよく引く
            $table->index(['buyer_id', 'completed_at'], 'idx_transactions_buyer_completed');
            $table->index(['seller_id', 'completed_at'], 'idx_transactions_seller_completed');

            // 「新着メッセージ順」の並び替えは messages 側の最新日時を使うが、
            // completed_at の判定が絡むケースも多いので一応
            $table->index(['completed_at', 'updated_at'], 'idx_transactions_completed_updated');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};