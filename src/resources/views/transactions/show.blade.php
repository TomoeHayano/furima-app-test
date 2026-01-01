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
                    <button type="button" class="transaction-chat__complete-btn" data-open-buyer-rating>
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
                        maxlength="500"
                        placeholder="取引メッセージを記入してください"
                    >{{ old('body') }}</textarea>

                    <div class="transaction-chat__composer-actions">
                        <label class="transaction-chat__image-btn">
                            画像を追加
                            <input type="file" name="image" accept="image/jpeg,image/png" hidden>
                        </label>

                        <button type="submit" class="transaction-chat__send-btn" aria-label="送信">
                            <img class="transaction-chat__send-icon" src="{{ asset('images/send-button.jpg') }}" alt="">
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>

{{-- 購入者：取引完了ボタン --}}
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

{{-- 出品者：購入者評価ダイアログ（購入者が完了した後に表示） --}}
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

  /**
   * FN009：本文のみ入力保持（transactionごとに保存）
   */
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

  /**
   * dialog共通：誤送信対策
   */
  function applySafeDialogBehavior(dialogElement) {
    if (!dialogElement) return;

    // ESCで閉じない
    dialogElement.addEventListener('cancel', function (event) {
      event.preventDefault();
    });

    // 背景クリックで閉じない（dialog要素そのものをクリックした時だけ）
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

  /**
   * 購入者：ボタン押下で評価ダイアログ表示
   */
  function focusFirstStar(dialogElement) {
    if (!dialogElement) return;
    const firstStar = dialogElement.querySelector('.rating-dialog__star');
    if (firstStar) {
      // showModal直後はフォーカス移動を遅延させた方が安定する
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

    // 必須要素が無ければ終了
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

    // clickが拾えないケース（クリック先が別要素）対策で、rootで拾う
    starsRoot.addEventListener('click', function (event) {
      const clickedButton = event.target.closest('.rating-dialog__star');
      if (!clickedButton) return;

      const selectedRating = Number(clickedButton.getAttribute('data-rating'));
      if (!Number.isFinite(selectedRating)) return;

      setRating(selectedRating);
    });

    // 既に値が入っていれば描画状態を反映（ブラウザ戻る対策）
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

  /**
   * 出品者：自動表示
   */
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
})();
</script>
@endsection
