@extends('layouts.app')

@section('title', 'その他の取引')

@push('css')
<link rel="stylesheet" href="{{ asset('css/transaction-show.css') }}">
@endpush

@section('content')
<div class="transaction-chat">
    {{-- エラー（FN008） --}}
    @if ($errors->any())
        <div class="transaction-chat__errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="transaction-chat__error">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="transaction-chat__layout">
        {{-- サイドバー（FN003/FN004/FN005） --}}
        <aside class="transaction-chat__sidebar">
            <h2 class="transaction-chat__sidebar-title">その他の取引</h2>

            <ul class="transaction-chat__sidebar-list">
                @foreach ($sidebarTransactions as $t)
                    @php
                        $product = $t->order->product;
                        $isActive = (int) $t->id === (int) $transaction->id;
                    @endphp

                    <li class="transaction-chat__sidebar-item {{ $isActive ? 'is-active' : '' }}">
                        <a class="transaction-chat__sidebar-link" href="{{ route('transactions.chat.show', $t) }}">
                            <div class="transaction-chat__sidebar-thumb-wrap">
                                <img class="transaction-chat__sidebar-thumb" src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                                @if ((int) $t->unread_count > 0)
                                    <span class="transaction-chat__badge">{{ $t->unread_count }}</span>
                                @endif
                            </div>
                            <div class="transaction-chat__sidebar-meta">
                                <div class="transaction-chat__sidebar-name">{{ $product->name }}</div>
                                <div class="transaction-chat__sidebar-price">¥{{ number_format($product->price) }}</div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- メイン --}}
        <main class="transaction-chat__main">
            @php
                $product = $transaction->order->product;
                $partnerName = $isBuyer ? $transaction->seller->name : $transaction->buyer->name;
            @endphp

            <header class="transaction-chat__header">
                <h1 class="transaction-chat__title">{{ $partnerName }}さんとの取引画面</h1>

                {{-- 購入者：取引完了ボタン（FN012） --}}
                @if ($shouldShowBuyerComplete)
                    <button type="button" class="transaction-chat__complete-btn" data-open-modal="buyer-complete-modal">
                        取引を完了する
                    </button>
                @endif
            </header>

            {{-- 商品情報 --}}
            <section class="transaction-chat__product">
                <img class="transaction-chat__product-image" src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                <div class="transaction-chat__product-info">
                    <div class="transaction-chat__product-name">{{ $product->name }}</div>
                    <div class="transaction-chat__product-price">¥{{ number_format($product->price) }}</div>
                </div>
            </section>

            {{-- メッセージ一覧 --}}
            <section class="transaction-chat__messages">
                @foreach ($messages as $m)
                    @php
                        $isMine = (int) $m->sender_id === (int) auth()->id();
                    @endphp

                    <div class="transaction-chat__message {{ $isMine ? 'is-mine' : 'is-other' }}">
                        <div class="transaction-chat__message-meta">
                            <span class="transaction-chat__message-sender">{{ $m->sender->name }}</span>
                            <span class="transaction-chat__message-time">{{ $m->created_at->format('Y/m/d H:i') }}</span>
                        </div>

                        <div class="transaction-chat__message-body">
                            {{ $m->body }}
                        </div>

                        @if ($m->image_path)
                            <div class="transaction-chat__message-image">
                                <img src="{{ asset('storage/' . $m->image_path) }}" alt="添付画像">
                            </div>
                        @endif

                        {{-- 編集/削除（FN010/FN011） --}}
                        @if ($isMine)
                            <details class="transaction-chat__message-actions">
                                <summary>編集 / 削除</summary>

                                <form method="post" action="{{ route('transactions.messages.update', [$transaction, $m]) }}">
                                    @csrf
                                    @method('patch')
                                    <input type="text" name="body" value="{{ old('body', $m->body) }}" maxlength="400">
                                    <button type="submit">更新</button>
                                </form>

                                <form method="post" action="{{ route('transactions.messages.destroy', [$transaction, $m]) }}">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" onclick="return confirm('削除しますか？')">削除</button>
                                </form>
                            </details>
                        @endif
                    </div>
                @endforeach
            </section>

            {{-- 投稿フォーム（FN006/FN007/FN009） --}}
            <section class="transaction-chat__composer">
                <form method="post" action="{{ route('transactions.messages.store', $transaction) }}" enctype="multipart/form-data">
                    @csrf

                    <textarea
                        id="chatBody"
                        name="body"
                        class="transaction-chat__textarea"
                        maxlength="400"
                        placeholder="取引メッセージを記入してください"
                    >{{ old('body') }}</textarea>

                    <div class="transaction-chat__composer-actions">
                        <label class="transaction-chat__image-btn">
                            画像を追加
                            <input type="file" name="image" accept="image/jpeg,image/png" hidden>
                        </label>

                        <button type="submit" class="transaction-chat__send-btn">送信</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>

{{-- 購入者：完了 + 出品者評価モーダル --}}
@if ($shouldShowBuyerComplete)
    <div class="modal" id="buyer-complete-modal" hidden>
        <div class="modal__content">
            <h2>取引完了（出品者を評価）</h2>
            <form method="post" action="{{ route('transactions.complete.buyer', $transaction) }}">
                @csrf
                <label>評価（必須）</label>
                <select name="rating" required>
                    <option value="">選択してください</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>

                <button type="submit">評価して完了</button>
                <button type="button" data-close-modal>閉じる</button>
            </form>
        </div>
    </div>
@endif

{{-- 出品者：購入者評価モーダル（購入者完了後に必須） --}}
@if ($shouldShowSellerRating)
    <div class="modal" id="seller-complete-modal">
        <div class="modal__content">
            <h2>取引完了（購入者を評価）</h2>
            <form method="post" action="{{ route('transactions.complete.seller', $transaction) }}">
                @csrf
                <label>評価（必須）</label>
                <select name="rating" required>
                    <option value="">選択してください</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>

                <button type="submit">評価して完了</button>
            </form>
        </div>
    </div>
@endif

<script>
(function () {
    // FN009：本文のみ入力保持（transactionごとに保存）
    const key = 'tx_chat_body_' + @json((string) $transaction->id);
    const textarea = document.getElementById('chatBody');
    if (textarea) {
        // 投稿成功時は削除（controller -> with('message_posted')）
        const posted = @json(session('message_posted') ? true : false);
        if (posted) localStorage.removeItem(key);

        const saved = localStorage.getItem(key);
        if (!textarea.value && saved) textarea.value = saved;

        textarea.addEventListener('input', function () {
            localStorage.setItem(key, textarea.value);
        });
    }

    // モーダル簡易制御
    document.querySelectorAll('[data-open-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.getAttribute('data-open-modal');
            const modal = document.getElementById(id);
            if (modal) modal.hidden = false;
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const modal = btn.closest('.modal');
            if (modal) modal.hidden = true;
        });
    });

    // 出品者モーダルは自動表示（hiddenにしてない）
})();
</script>
@endsection