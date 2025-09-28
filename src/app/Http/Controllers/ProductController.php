<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
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

        // 全商品を取得（ログイン中は自分の商品を除外）
        $query = Product::query()
            ->when(Auth::check(), function ($q) {
                $q->where('user_id', '!=', Auth::id());
            });

        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        $products = $query->get(['id', 'name', 'image_path', 'is_sold']);

        return view('products.index', [
            'products' => $products,
            'tab' => 'recommend',
            'guest' => Auth::guest(),
        ]);
    }

    /**
     * マイリスト一覧
     */
    public function mylist(Request $request)
    {
        $tab = $request->query('tab', 'mylist');
        $keyword = $request->input('keyword');

        if (Auth::guest()) {
            // ゲストユーザーはマイリストを表示しない
            return view('products.index', [
                'products' => collect(),
                'tab' => $tab,
                'guest' => true,
            ]);
        }

        if ($tab === 'recommend') {
            // ログイン中は自分の商品を除外して全商品
            $query = Product::query()
                ->where('user_id', '!=', Auth::id());
        } else {
            // いいねした商品のみ
            $query = Product::whereHas('likes', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        $products = $query->get(['id', 'name', 'image_path', 'is_sold']);

        return view('products.index', [
            'products' => $products,
            'tab' => $tab,
            'guest' => false,
        ]);
    }
    /**
     * 商品詳細
     */
    public function show($item_id)
    {
        $product = Product::with(['comments.user', 'categories', 'condition'])
            ->findOrFail($item_id);

        // 合計いいね数
        $likesCount = $product->likes()->count();

        // ログインしている場合のいいね状態
        $liked = Auth::check()
            ? $product->likes()->where('user_id', Auth::id())->exists()
            : false;

        return view('products.show', compact('product', 'liked', 'likesCount'));
    }

    /**
     * コメント投稿
     */
    public function storeComment(CommentRequest $request, $productId)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('products.show', ['item_id' => $productId]);
    }

    /**
     * いいねの切り替え
     */
    public function toggleLike($productId)
    {
        // ログイン必須
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Auth::id();

        // すでにいいねしているか確認
        $like = Like::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->first();

        if ($like) {
            // いいね解除
            $like->delete();
            $status = 'unliked';
        } else {
            // いいね追加
            Like::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $status = 'liked';
        }

        // 現在のいいね数をカウント
        $likesCount = Like::where('product_id', $productId)->count();

        return response()->json([
            'status' => $status,
            'likesCount' => $likesCount,
        ]);
    }
}
