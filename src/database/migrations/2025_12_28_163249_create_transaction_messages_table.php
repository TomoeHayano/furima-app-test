<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_messages', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->cascadeOnDelete();

            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 本文（必須・最大1000文字）
            $table->string('body', 1000);

            // 画像（任意）: public disk に保存した相対パスなどを入れる想定
            $table->string('image_path', 255)->nullable();

            // 未読管理（相手が読んだら時刻を入れる）
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // 取引チャット表示/ソートで効きやすいインデックス
            $table->index(['transaction_id', 'created_at'], 'idx_transaction_messages_transaction_created');
            $table->index(['sender_id', 'created_at'], 'idx_transaction_messages_sender_created');
            $table->index(['transaction_id', 'read_at'], 'idx_transaction_messages_transaction_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_messages');
    }
};