<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCondition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SellController extends Controller
{
    // 出品画面表示
    public function create()
    {
        $categories = Category::all();
        $conditions = ProductCondition::all();

        return view('products.create', compact('categories', 'conditions'));
    }

    // 出品処理
    public function store(ExhibitionRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $path     = $request->file('image')->store('products', 'public');
            $imageUrl = Storage::url($path);

            $product = Product::create([
                'user_id'      => Auth::id(),
                'name'         => $request->name,
                'brand_name'   => $request->brand_name,
                'description'  => $request->description,
                'price'        => $request->price,
                'condition_id' => $request->condition_id,
                'image_path'   => $imageUrl,
                'is_sold'      => false,
            ]);

            // 中間テーブルにカテゴリ保存（複数選択対応）
            $product->categories()->sync($request->category_ids);

            return $product;
        });

        return redirect()
            ->route('user.mypage', ['page' => 'sell'])
            ->with('success', '商品を出品しました！');
    }
}
