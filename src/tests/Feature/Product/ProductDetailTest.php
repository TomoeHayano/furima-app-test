<?php

namespace Tests\Feature\Product;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Product;
use App\Models\ProductCondition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品詳細ページに必要な情報が表示されることを確認する
     */
    public function test_商品詳細ページに_必要な情報が表示される(): void
    {
        $seller = User::factory()->create();
        $condition = ProductCondition::factory()->create([
            'status_name' => '新品同様',
        ]);

        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'name' => '高級カメラ',
            'brand_name' => 'Canon',
            'price' => 123456,
            'description' => '高性能なカメラです。',
            'image_path' => 'http://example.com/camera.jpg',
            'condition_id' => $condition->id,
        ]);

        $categories = collect([
            Category::factory()->create(['name' => '家電']),
            Category::factory()->create(['name' => 'カメラ']),
        ]);
        $product->categories()->attach($categories->pluck('id'));

        $commentUsers = User::factory()->count(2)->create();
        foreach ($commentUsers as $index => $user) {
            Comment::factory()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'content' => 'コメント内容' . ($index + 1),
            ]);
        }

        $likeUsers = User::factory()->count(2)->create();
        foreach ($likeUsers as $user) {
            Like::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
        }

        $response = $this->get("/item/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee('http://example.com/camera.jpg', false);
        $response->assertSee('高級カメラ');
        $response->assertSee('Canon');
        $response->assertSee('¥123,456 (税込)');
        $response->assertSee('高性能なカメラです。');
        $response->assertSee('新品同様');
        $response->assertSee('コメント (2)');
        $response->assertSee('id="likes-count">2<', false);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }

        foreach ($commentUsers as $index => $user) {
            $response->assertSee($user->name);
            $response->assertSee('コメント内容' . ($index + 1));
        }
    }

    /**
     * 複数選択されたカテゴリがすべて表示されることを確認する
     */
    public function test_商品詳細ページに_複数カテゴリが表示される(): void
    {
        $condition = ProductCondition::factory()->create();
        $product = Product::factory()->create([
            'condition_id' => $condition->id,
        ]);

        $categories = Category::factory()->count(3)->sequence(
            ['name' => 'メンズ'],
            ['name' => 'ファッション'],
            ['name' => 'アクセサリー']
        )->create();

        $product->categories()->attach($categories->pluck('id'));

        $response = $this->get("/item/{$product->id}");

        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
