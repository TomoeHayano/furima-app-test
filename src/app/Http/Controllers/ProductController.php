<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
        public function index()
    {
        // ゲスト用おすすめ一覧
        $products = [
            ['name' => 'おすすめ商品1', 'is_sold' => false],
            ['name' => 'おすすめ商品2', 'is_sold' => true],
        ];

        return view('products.index', [
            'products' => $products,
            'tab' => 'recommend',
            'guest' => true, // Blade 側で「おすすめだけ表示」に使う
        ]);
    }

    public function mylist(Request $request)
    {
        $tab = $request->query('tab', 'mylist'); // デフォルトは mylist

        if ($tab === 'recommend') {
            // ログイン後のおすすめ（全商品）
            $products = [
                ['name' => 'おすすめ商品A', 'is_sold' => false],
                ['name' => 'おすすめ商品B', 'is_sold' => true],
            ];
        } else {
            // マイリスト
            $products = [
                ['name' => 'マイリスト商品1', 'is_sold' => false],
                ['name' => 'マイリスト商品2', 'is_sold' => true],
            ];
        }

        return view('products.index', [
            'products' => $products,
            'tab' => $tab,
            'guest' => false, // Blade 側で「マイリストタブも表示」に使う
        ]);
    }
}