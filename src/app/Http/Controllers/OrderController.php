<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * 購入画面表示
     */
    public function create($item_id)
    {   
        $product = Product::with('user')->findOrFail($item_id);
        $user = Auth::user()->load('profile');

        return view('products.order', compact('product', 'user'));
    }

    /**
     * Stripe 決済開始（購入処理）
     */
    public function store(PurchaseRequest $request, $item_id)
    {
        $product = Product::findOrFail($item_id);

        // すでに売り切れの場合は購入不可
        if ($product->is_sold) {
            return redirect()->back()->withErrors(['商品はすでに売り切れています']);
        }

        $paymentMethod = $request->input('payment_method');

        Stripe::setApiKey(config('services.stripe.secret'));

        // 支払い方法ごとに設定
        if ($paymentMethod === 'カード支払い') {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $product->name],
                        'unit_amount' => $product->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success', $product->id),
                'cancel_url'  => route('purchase.cancel'),
            ]);
        } elseif ($paymentMethod === 'コンビニ支払い') {
            $session = Session::create([
                'payment_method_types' => ['konbini'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $product->name],
                        'unit_amount' => $product->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success', $product->id),
                'cancel_url'  => route('purchase.cancel'),
            ]);
        } else {
            return back()->withErrors(['支払い方法を選択してください']);
        }

        // Stripe Checkout へリダイレクト
        return redirect($session->url);
    }

    /**
     * 決済成功時
     */
    public function success($item_id)
    {
        $product = Product::findOrFail($item_id);

        // 注文を保存
        Order::create([
            'user_id'        => Auth::id(),
            'product_id'     => $product->id,
            'profile_id'     => Auth::user()->profile->id ?? null,
            'payment_method' => 'Stripeテスト決済',
            'address'        => Auth::user()->profile->address ?? '',
        ]);

        // 商品を売り切れに更新
        $product->update(['is_sold' => 1]);

        return redirect()->route('products.index')->with('success', '決済が完了しました！');
    }

    /**
     * キャンセル時
     */
    public function cancel()
    {
        return redirect()->route('products.index')->with('error', '決済がキャンセルされました');
    }
}