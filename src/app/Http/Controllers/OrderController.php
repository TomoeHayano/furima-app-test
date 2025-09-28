<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * 購入画面表示
     */
    public function create($item_id)
    {
        $product = Product::with('user')->findOrFail($item_id);
        $user = Auth::user();

        return view('products.order', compact('product', 'user'));
    }

    /**
     * 購入処理
     */
    public function store(PurchaseRequest $request, $item_id)
    {

        $product = Product::findOrFail($item_id);

        // すでに売り切れの場合は購入不可
        if ($product->is_sold) {
            return redirect()->back()->withErrors(['商品はすでに売り切れています']);
        }

        // 注文情報を保存
        Order::create([
            'user_id'       => Auth::id(),
            'product_id'    => $product->id,
            'profile_id'    => null, 
            'payment_method'=> $request->payment_method,
            'address'       => $request->address,
        ]);

        // 商品を「売り切れ」に更新
        $product->update(['is_sold' => 1]);

        // 商品一覧にリダイレクト
        return redirect()->route('products.index')->with('success', '購入が完了しました！');
    }
}
