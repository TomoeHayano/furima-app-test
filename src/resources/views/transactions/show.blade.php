@extends('layouts.app')

@section('title', 'その他の取引')

@push('css')
<link rel="stylesheet" href="{{ asset('css/transaction-show.css') }}">
@endpush

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $imageUrl = function (?string $path): string {
        if (!$path) {
            return asset('images/no-image.png');
        }
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        $normalized = ltrim($path, '/');
        if (Str::startsWith($normalized, 'public/')) {
            $normalized = Str::after($normalized, 'public/');
        }
        return Storage::url($normalized);
    };
@endphp

@section('content')
<div class="transaction-chat">
    <div class="transaction-chat__layout">
        <aside class="transaction-chat__sidebar">
            <h2 class="transaction-chat__sidebar-title">その他の取引</h2>

            <ul class="transaction-chat__sidebar-list">
                @foreach ($sidebarTransactions as $sidebarTransaction)
                    @php
                        $product = $sidebarTransaction->order->product;
                        $isActive = (int) $sidebarTransaction->id === (int) $transaction->id;
                    @endphp

                    <li class="transaction-chat__sidebar-item {{ $isActive ? 'is-active' : '' }}">
                        <a class="transaction-chat__sidebar-link" href="{{ route('transactions.chat.show', $sidebarTransaction) }}">
                            <div class="transaction-chat__sidebar-thumb-wrap">
                                <img class="transaction-chat__sidebar-thumb" src="{{ $imageUrl(optional($product)->image_path) }}" alt="{{ $product->name }}">
                                @if ((int) $sidebarTransaction->unread_count > 0)
                                    <span class="transaction-chat__badge">{{ $sidebarTransaction->unread_count }}</span>
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
                $partnerProfile = $isBuyer ? optional($transaction->seller)->profile : optional($transaction->buyer)->profile;
                $partnerAvatar = $imageUrl(optional($partnerProfile)->image_path);
            @endphp

            <header class="transaction-chat__header">
                <img class="transaction-chat__header-avatar" src="{{ $partnerAvatar }}" alt="{{ $partnerName }}">
                <h1 class="transaction-chat__title">{{ $partnerName }}さんとの取引画面</h1>

                @if ($isFullyCompleted)
                    <span class="transaction-chat__complete-label">取引が完了しました</span>
                @elseif ($shouldShowBuyerComplete)
                    <button type="button" class="transaction-chat__complete-btn" data-open-buyer-rating>
                        取引を完了する
                    </button>
                @endif
            </header>

            <section class="transaction-chat__product">
                <img class="transaction-chat__product-image" src="{{ $imageUrl(optional($product)->image_path) }}" alt="{{ $product->name }}">
                <div class="transaction-chat__product-info">
                    <div class="transaction-chat__product-name">{{ $product->name }}</div>
                    <div class="transaction-chat__product-price">¥{{ number_format($product->price) }}</div>
                </div>
            </section>

            <section class="transaction-chat__messages">
                @foreach ($messages as $message)
                    @php
                        $isMine = (int) $message->sender_id === (int) auth()->id();
                        $senderAvatar = $imageUrl(optional($message->sender->profile)->image_path);
                    @endphp

                    <div class="transaction-chat__message {{ $isMine ? 'is-mine' : 'is-other' }}">
                        <img class="transaction-chat__message-avatar" src="{{ $senderAvatar }}" alt="{{ $message->sender->name }}">
                        <div class="transaction-chat__message-meta">
                            <span class="transaction-chat__message-sender">{{ $message->sender->name }}</span>
                        </div>

                        <div class="transaction-chat__message-body">
                            {{ $message->body }}
                        </div>

                        @if ($message->image_path)
                            <div class="transaction-chat__message-image">
                                <img src="{{ $imageUrl($message->image_path) }}" alt="添付画像">
                            </div>
                        @endif

                        @if ($isMine && ! $isFullyCompleted)
                            <div class="transaction-chat__message-actions is-mine">
                                <div class="transaction-chat__action-triggers">
                                  <button type="button" class="transaction-chat__action-trigger" data-action="edit-{{ $message->id }}">編集</button>
                                  <button type="button" class="transaction-chat__action-trigger" data-action="delete-{{ $message->id }}">削除</button>
                                </div>

                                <form
                                  method="post"
                                  action="{{ route('transactions.messages.update', [$transaction, $message]) }}"
                                  class="transaction-chat__action-panel"
                                  data-panel="edit-{{ $message->id }}"
                                >
                                    @csrf
                                    @method('patch')
                                    <input type="text" name="body" value="{{ old('body', $message->body) }}" maxlength="400">
                                    <div class="transaction-chat__action-panel-buttons">
                                      <button type="submit">更新</button>
                                      <button type="button" class="transaction-chat__action-close">キャンセル</button>
                                    </div>
                                </form>

                                <form
                                  method="post"
                                  action="{{ route('transactions.messages.destroy', [$transaction, $message]) }}"
                                  class="transaction-chat__action-panel"
                                  data-panel="delete-{{ $message->id }}"
                                >
                                    @csrf
                                    @method('delete')
                                    <div class="transaction-chat__action-panel-buttons">
                                      <button type="submit">削除</button>
                                      <button type="button" class="transaction-chat__action-close">キャンセル</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </section>

            <section class="transaction-chat__composer {{ $errors->any() ? 'has-errors' : '' }}">
                @if ($errors->any())
                    <ul class="transaction-chat__form-errors">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="post" action="{{ route('transactions.messages.store', $transaction) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="transaction-chat__composer-row">
                        <textarea
                            id="chatBody"
                            name="body"
                            class="transaction-chat__textarea"
                            maxlength="500"
                            placeholder="取引メッセージを記入してください"
                            @if ($isFullyCompleted) disabled @endif
                        >{{ old('body') }}</textarea>

                        <div class="transaction-chat__composer-actions">
                            <label class="transaction-chat__image-btn" @if ($isFullyCompleted) aria-disabled="true" style="pointer-events:none; opacity:0.6;" @endif>
                                画像を追加
                                <input type="file" name="image" hidden @if ($isFullyCompleted) disabled @endif>
                            </label>

                            <button type="submit" class="transaction-chat__send-btn" aria-label="送信" @if ($isFullyCompleted) disabled style="opacity:0.5; cursor:not-allowed;" @endif>
                                <img class="transaction-chat__send-icon" src="{{ asset('images/send-button.jpg') }}" alt="">
                            </button>
                        </div>
                    </div>
                </form>
                @if ($isFullyCompleted)
                    <p class="transaction-chat__completed-note">取引は完了しています。メッセージの送信・編集はできません。</p>
                @endif
            </section>
        </main>
    </div>
</div>

{{-- 購入者：出品者評価ダイアログ --}}
@if ($shouldShowBuyerComplete)
  <dialog class="rating-dialog" id="buyerRatingDialog">
    <div class="rating-dialog__inner">
      <p class="rating-dialog__title">取引が完了しました。</p>
      <p class="rating-dialog__subtitle">今回の取引相手はどうでしたか？</p>

      <form method="POST" action="{{ route('transactions.complete.buyer', $transaction) }}">
        @csrf

        <input type="hidden" name="rating" id="buyerRatingValue" required>

        <div class="rating-dialog__stars" data-rating-root="buyer">
          @for ($rating = 1; $rating <= 5; $rating++)
            <button
              type="button"
              class="rating-dialog__star"
              data-rating="{{ $rating }}"
              aria-label="評価 {{ $rating }}"
            ></button>
          @endfor
        </div>

        <button type="submit" class="rating-dialog__submit" disabled>送信する</button>
      </form>
    </div>
  </dialog>
@endif

{{-- 出品者：購入者評価ダイアログ --}}
@if ($shouldShowSellerRating)
  <dialog class="rating-dialog" id="sellerRatingDialog">
    <div class="rating-dialog__inner">
      <p class="rating-dialog__title">取引が完了しました。</p>
      <p class="rating-dialog__subtitle">今回の取引相手はどうでしたか？</p>

      <form method="POST" action="{{ route('transactions.complete.seller', $transaction) }}">
        @csrf

        <input type="hidden" name="rating" id="sellerRatingValue" required>

        <div class="rating-dialog__stars" data-rating-root="seller">
          @for ($rating = 1; $rating <= 5; $rating++)
            <button
              type="button"
              class="rating-dialog__star"
              data-rating="{{ $rating }}"
              aria-label="評価 {{ $rating }}"
            ></button>
          @endfor
        </div>

        <button type="submit" class="rating-dialog__submit" disabled>送信する</button>
      </form>
    </div>
  </dialog>
@endif

<script>
(function () {
    'use strict';

    const transactionId = @json((string) $transaction->id);
    const storageKey = 'tx_chat_body_' + transactionId;
    const chatTextarea = document.getElementById('chatBody');

    if (chatTextarea) {
        const isPosted = @json(session('message_posted') ? true : false);
        if (isPosted) {
            localStorage.removeItem(storageKey);
        }

        const savedText = localStorage.getItem(storageKey);
        if (!chatTextarea.value && savedText) {
            chatTextarea.value = savedText;
        }

        chatTextarea.addEventListener('input', function () {
            localStorage.setItem(storageKey, chatTextarea.value);
        });
    }

    function applySafeDialogBehavior(dialogElement) {
        if (!dialogElement) return;

        // ESCで閉じない
        dialogElement.addEventListener('cancel', function (event) {
            event.preventDefault();
        });

        // 背景クリックで閉じない
        dialogElement.addEventListener('click', function (event) {
            if (event.target === dialogElement) {
                event.preventDefault();
            }
        });

        // タブ切替/別タブクリックで閉じる
        function closeIfOpen() {
            if (dialogElement.open) {
                dialogElement.close();
            }
        }

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) closeIfOpen();
        });

        window.addEventListener('blur', closeIfOpen);
    }

    function focusFirstStar(dialogElement) {
        if (!dialogElement) return;
        const firstStar = dialogElement.querySelector('.rating-dialog__star');
        if (firstStar) {
            setTimeout(function () {
                firstStar.focus();
            }, 0);
        }
    }

    function setupRatingDialog(dialogElement, hiddenInputElement) {
        if (!dialogElement || !hiddenInputElement) return;

        const starsRoot =
            dialogElement.querySelector('[data-rating-root]') ||
            dialogElement.querySelector('.rating-dialog__stars');

        const submitButton = dialogElement.querySelector('.rating-dialog__submit');
        const starButtons = dialogElement.querySelectorAll('.rating-dialog__star');

        if (!starsRoot || !submitButton || starButtons.length === 0) {
            return;
        }

        function setRating(selectedRating, enableSubmit = true) {
            hiddenInputElement.value = String(selectedRating);

            starButtons.forEach(function (button) {
                const ratingValue = Number(button.getAttribute('data-rating'));
                const shouldActive = ratingValue <= selectedRating;
                button.classList.toggle('is-active', shouldActive);
            });

            if (enableSubmit) {
                submitButton.disabled = false;
            }
        }

        starsRoot.addEventListener('click', function (event) {
            const clickedButton = event.target.closest('.rating-dialog__star');
            if (!clickedButton) return;

            const selectedRating = Number(clickedButton.getAttribute('data-rating'));
            if (!Number.isFinite(selectedRating)) return;

            setRating(selectedRating);
        });

        const initialValue = Number(hiddenInputElement.value);
        if (Number.isFinite(initialValue) && initialValue > 0) {
            setRating(initialValue, false);
        }
    }

    const buyerOpenButton = document.querySelector('[data-open-buyer-rating]');
    const buyerDialog = document.getElementById('buyerRatingDialog');
    const buyerHiddenInput = document.getElementById('buyerRatingValue');

    if (buyerDialog) {
        applySafeDialogBehavior(buyerDialog);
        setupRatingDialog(buyerDialog, buyerHiddenInput);

        if (buyerOpenButton) {
            buyerOpenButton.addEventListener('click', function () {
                if (!buyerDialog.open) {
                    buyerDialog.showModal();
                    focusFirstStar(buyerDialog);
                }
            });
        }
    }

    //出品者：自動表示
    const sellerDialog = document.getElementById('sellerRatingDialog');
    const sellerHiddenInput = document.getElementById('sellerRatingValue');

    if (sellerDialog) {
        applySafeDialogBehavior(sellerDialog);
        setupRatingDialog(sellerDialog, sellerHiddenInput);

        if (!sellerDialog.open) {
            sellerDialog.showModal();
            focusFirstStar(sellerDialog);
        }
    }

    // メッセージ編集・削除パネル制御
    const actionTriggers = document.querySelectorAll('.transaction-chat__action-trigger');
    const actionPanels = document.querySelectorAll('.transaction-chat__action-panel');
    const actionContainers = document.querySelectorAll('.transaction-chat__message-actions');

    function closeAllPanels() {
        actionPanels.forEach(function (panel) {
            panel.classList.remove('is-open');
        });
        actionContainers.forEach(function (container) {
            container.classList.remove('has-open-panel');
        });
    }

    actionTriggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const target = trigger.getAttribute('data-action');
            if (!target) return;
            const panel = document.querySelector('.transaction-chat__action-panel[data-panel="' + target + '"]');
            if (!panel) return;
            const container = trigger.closest('.transaction-chat__message-actions');

            const isOpen = panel.classList.contains('is-open');
            closeAllPanels();
            if (!isOpen) {
                panel.classList.add('is-open');
                if (container) container.classList.add('has-open-panel');
            }
        });
    });

    document.querySelectorAll('.transaction-chat__action-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closeAllPanels();
        });
    });
})();
</script>
@endsection
