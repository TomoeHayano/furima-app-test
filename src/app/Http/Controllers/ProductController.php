<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        // ゲスト用おすすめ一覧
        $products = [
            ['name' => 'おすすめ商品1', 'is_sold' => false],
            ['name' => 'おすすめ商品2', 'is_sold' => true],
        ];

        // 部分一致検索（ゲスト用）
        if ($keyword) {
            $products = array_filter($products, function ($product) use ($keyword) {
                return str_contains($product['name'], $keyword);
            });
        }

        return view('products.index', [
            'products' => $products,
            'tab' => 'recommend',
            'guest' => true,
        ]);
    }

    public function mylist(Request $request)
    {
        $tab = $request->query('tab', 'mylist');
        $keyword = $request->input('keyword');

        if ($tab === 'recommend') {
            $products = [
                ['name' => 'おすすめ商品A', 'is_sold' => false],
                ['name' => 'おすすめ商品B', 'is_sold' => true],
            ];
        } else {
            $products = [
                ['name' => 'マイリスト商品1', 'is_sold' => false],
                ['name' => 'マイリスト商品2', 'is_sold' => true],
            ];
        }

        // 部分一致検索（ログイン後）
        if ($keyword) {
            $products = array_filter($products, function ($product) use ($keyword) {
                return str_contains($product['name'], $keyword);
            });
        }

        return view('products.index', [
            'products' => $products,
            'tab' => $tab,
            'guest' => false,
        ]);
    }

    public function show($item_id)
    {
        // ダミーデータ（実装時は Product::with(['categories','comments','likes'])->findOrFail($item_id)）
        $product = [
            'id' => $item_id,
            'name' => 'サンプル商品',
            'brand_name' => 'ブランドX',
            'price' => 47000,
            'likes' => 3,
            'comments_count' => 1,
            'description' => '商品の状態は良好です。傷もありません。',
            'categories' => ['洋服', 'メンズ'],
            'condition' => '良好',
            'comments' => [
                ['user' => 'admin', 'content' => 'こちらにコメントが入ります。']
            ]
        ];

        return view('products.show', compact('product'));
    }
}