<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;


class ProductController extends Controller
{
    /**
     * ゲスト用おすすめ一覧
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $products = [
            ['id' => 13, 'name' => 'おすすめ商品1', 'is_sold' => false],
            ['id' => 14, 'name' => 'おすすめ商品2', 'is_sold' => true],
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
                ['id' => 15, 'name' => 'おすすめ商品A', 'is_sold' => false],
                ['id' => 16, 'name' => 'おすすめ商品B', 'is_sold' => true],
            ];
        } else {
            $products = [
                ['id' => 17, 'name' => 'マイリスト商品1', 'is_sold' => false],
                ['id' => 18, 'name' => 'マイリスト商品2', 'is_sold' => true],
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

    // public function show($itemId)
    // {
    //     // 仮データ（本番はDBから取得）
    //     $product = [
    //         'id' => $itemId,
    //         'name' => "商品サンプル #{$itemId}",
    //         'brand' => 'ブランド名サンプル',
    //         'price' => 47000,
    //         'description' => 'カラー：グレー 新品 商品の状態は良好です。',
    //         'categories' => ['洋服', 'メンズ'],
    //         'condition' => '良好',
    //         'comments' => [
    //             ['user' => 'admin', 'content' => 'こちらにコメントが入ります。'],
    //         ],
    //     ];

    //     $product = \App\Models\Product::with(['comments.user', 'categories', 'condition'])
    //     ->findOrFail($itemId);

    //     // 合計いいね数
    //     $likesCount = \App\Models\Like::where('product_id', $itemId)->count();

    //     // ログインしてる場合だけ判定
    //     $liked = auth()->check() 
    //         ? \App\Models\Like::where('user_id', auth()->id())
    //             ->where('product_id', $itemId)
    //             ->exists()
    //         : false;

    //     return view('products.show', compact('product', 'liked', 'likesCount'));
    // }

    public function toggleLike($productId)
    {
        // ログイン必須
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();

        // すでにいいねしているか確認
        $like = \App\Models\Like::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->first();

        if ($like) {
            // いいね解除
            $like->delete();
            $status = 'unliked';
        } else {
            // いいね追加
            \App\Models\Like::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $status = 'liked';
        }

        // 現在のいいね数をカウント
        $likesCount = \App\Models\Like::where('product_id', $productId)->count();

        return response()->json([
            'status' => $status,
            'likesCount' => $likesCount,
        ]);
    }

    public function show($item_id)
    {
        $product = \App\Models\Product::with(['comments.user', 'categories', 'condition'])
            ->findOrFail($item_id);

        $likesCount = $product->likes()->count();

        $liked = auth()->check()
            ? $product->likes()->where('user_id', auth()->id())->exists()
            : false;

        return view('products.show', compact('product', 'liked', 'likesCount'));
    }

    public function storeComment(CommentRequest $request, $productId)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('products.show', ['item_id' => $productId]);
    }
}
