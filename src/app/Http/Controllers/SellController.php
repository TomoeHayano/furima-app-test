<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCondition;

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
        $path = $request->file('image')->store('products', 'public');

        $product = Product::create([
            'user_id'      => Auth::id(),
            'name'         => $request->name,
            'brand_name'   => $request->brand_name,
            'description'  => $request->description,
            'price'        => $request->price,
            'condition_id' => $request->condition_id,
            'image_path'   => $path,
            'is_sold'      => 0,
        ]);

        // 中間テーブルにカテゴリ保存（複数選択対応）
        $product->categories()->attach($request->category_ids);

        return redirect()->route('index')->with('success', '商品を出品しました！');
    }
}