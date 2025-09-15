<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * ゲスト用おすすめ一覧
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $products = [
            ['id' => 1, 'name' => 'おすすめ商品1', 'is_sold' => false],
            ['id' => 2, 'name' => 'おすすめ商品2', 'is_sold' => true],
        ];

        // 部分一致検索
        if ($keyword) {
            $products = array_values(array_filter($products, function ($product) use ($keyword) {
                return str_contains($product['name'], $keyword);
            }));
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
                ['id' => 3, 'name' => 'おすすめ商品A', 'is_sold' => false],
                ['id' => 4, 'name' => 'おすすめ商品B', 'is_sold' => true],
            ];
        } else {
            $products = [
                ['id' => 5, 'name' => 'マイリスト商品1', 'is_sold' => false],
                ['id' => 6, 'name' => 'マイリスト商品2', 'is_sold' => true],
            ];
        }

        // 部分一致検索
        if ($keyword) {
            $products = array_values(array_filter($products, function ($product) use ($keyword) {
                return str_contains($product['name'], $keyword);
            }));
        }

        return view('products.index', [
            'products' => $products,
            'tab' => $tab,
            'guest' => false,
        ]);
    }

    /**
     * 商品詳細画面
     */
    public function show($itemId)
    {
        // 仮データ（本番はDBから取得）
        $product = [
            'id' => $itemId,
            'name' => "商品サンプル #{$itemId}",
            'brand' => 'ブランド名サンプル',
            'price' => 47000,
            'description' => 'カラー：グレー 新品 商品の状態は良好です。',
            'categories' => ['洋服', 'メンズ'],
            'condition' => '良好',
            'likes' => 3,
            'liked' => false,
            'comments' => [
                ['user' => 'admin', 'content' => 'こちらにコメントが入ります。'],
            ],
        ];

        return view('products.show', compact('product'));
    }
}
