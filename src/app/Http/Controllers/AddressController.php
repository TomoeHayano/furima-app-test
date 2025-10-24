<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
  // 住所変更画面表示
  public function edit($item_id)
  {
    $product         = Product::findOrFail($item_id);
    $profile         = Auth::user()->profile;
    $shippingAddress = session("shipping_addresses.{$product->id}");

    return view('products.edit', [
      'product'         => $product,
      'profile'         => $profile,
      'shippingAddress' => $shippingAddress,
    ]);
  }

  // 住所更新処理
  public function update(AddressRequest $request, $item_id)
  {
    $product = Product::findOrFail($item_id);

    session([
      "shipping_addresses.{$product->id}" => [
        'postal_code'   => $request->postal_code,
        'address'       => $request->address,
        'building_name' => $request->building_name,
      ],
    ]);

    // 更新後は購入画面へ戻す
    return redirect()->route('purchase.create', $product->id)
            ->with('success', '住所を更新しました！');
  }
}
