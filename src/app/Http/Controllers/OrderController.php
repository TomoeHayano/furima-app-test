<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class OrderController extends Controller
{
    /**
     * 購入画面表示
     */
    public function create($itemId)
    {
        $product         = Product::with('user')->findOrFail($itemId);
        /** @var User $user */
        $user = Auth::user();
        $user->load('profile');
        $shippingAddress = session("shipping_addresses.{$product->id}");

        return view('products.order', compact('product', 'user', 'shippingAddress'));
    }

    /**
     * Stripe 決済開始（購入処理）
     */
    public function store(PurchaseRequest $request, $itemId)
    {
        $product         = Product::findOrFail($itemId);
        $shippingAddress = session("shipping_addresses.{$product->id}");
        $profile         = Auth::user()->profile;

        // すでに売り切れの場合は購入不可
        if ($product->is_sold) {
            return redirect()->back()->withErrors(['商品はすでに売り切れています']);
        }

        if (! $shippingAddress && ! $profile) {
            return redirect()->back()->withErrors(['address' => '配送先住所を登録してください。']);
        }

        $paymentMethod = $request->input('payment_method');

        Stripe::setApiKey(config('services.stripe.secret'));

        // 支払い方法ごとに設定
        if ($paymentMethod === 'カード支払い') {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => [[
                    'price_data' => [
                        'currency'     => 'jpy',
                        'product_data' => ['name' => $product->name],
                        'unit_amount'  => $product->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => route('purchase.success', $product->id),
                'cancel_url'  => route('purchase.cancel'),
            ]);
        } elseif ($paymentMethod === 'コンビニ支払い') {
            $session = Session::create([
                'payment_method_types' => ['konbini'],
                'line_items'           => [[
                    'price_data' => [
                        'currency'     => 'jpy',
                        'product_data' => ['name' => $product->name],
                        'unit_amount'  => $product->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
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
    public function success($itemId)
    {
        $product         = Product::findOrFail($itemId);
        $shippingAddress = session("shipping_addresses.{$product->id}");
        $profile         = Auth::user()->profile;
        $profileId       = optional($profile)->id;

        // 注文を保存
        $useSessionShipping = is_array($shippingAddress);

        $order = Order::create([
            'user_id'              => Auth::id(),
            'product_id'           => $product->id,
            'profile_id'           => $profileId,
            'payment_method'       => 'Stripeテスト決済',
            'shipping_postal_code' => $useSessionShipping
                ? data_get($shippingAddress, 'postal_code')
                : optional($profile)->postal_code,
            'shipping_address' => $useSessionShipping
                ? data_get($shippingAddress, 'address')
                : optional($profile)->address,
            'shipping_building_name' => $useSessionShipping
                ? data_get($shippingAddress, 'building_name', '')
                : optional($profile)->building_name,
        ]);

        session()->forget("shipping_addresses.{$product->id}");

        // 商品を売り切れに更新
        $product->update(['is_sold' => 1]);

        // 取引ルーム作成（1注文=1取引）
        Transaction::firstOrCreate(
            ['order_id' => $order->id],
            [
                'buyer_id'  => Auth::id(),
                'seller_id' => $product->user_id,
            ]
        );

        // 取引中タブへ遷移
        return redirect()
            ->route('user.mypage', ['page' => 'progress'])
            ->with('success', '決済が完了しました');
    }

    /**
     * キャンセル時
     */
    public function cancel()
    {
        return redirect()->route('products.index')->with('error', '決済がキャンセルされました');
    }
}
