<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ゲストもログイン後も共通：商品一覧
    public function index(Request $request)
    {
        if ($request->query('tab') === 'mylist') {
            // マイリスト用ダミーデータ
            $products = [
                ['name' => 'マイリスト商品1', 'is_sold' => false],
                ['name' => 'マイリスト商品2', 'is_sold' => true],
                ['name' => 'マイリスト商品3', 'is_sold' => false],
            ];
        } else {
            // おすすめ用ダミーデータ
            $products = [
                ['name' => 'おすすめ商品A', 'is_sold' => false],
                ['name' => 'おすすめ商品B', 'is_sold' => true],
                ['name' => 'おすすめ商品C', 'is_sold' => false],
            ];
        }

        return view('products.index', compact('products'));
    }
}
