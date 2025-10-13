<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 部分一致で商品名を検索できることを確認する
     */
    public function test_商品名で部分一致検索ができる(): void
    {
        Product::factory()->create(['name' => 'ビンテージカメラ']);
        $unrelated = Product::factory()->create(['name' => 'アウトドアテント']);

        $response = $this->get('/?keyword=カメラ');

        $response->assertStatus(200);
        $response->assertSee('ビンテージカメラ');
        $response->assertDontSee($unrelated->name);
    }

    /**
     * 検索キーワードがマイリストタブへの遷移後も保持されることを確認する
     */
    public function test_検索状態がマイリストでも保持される(): void
    {
        $user = User::factory()->create();
        Product::factory()->create(['name' => 'カメラバッグ']);

        $keyword = 'カメラ';

        $homeResponse = $this->actingAs($user)->get("/?keyword={$keyword}");
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('tab=mylist&amp;keyword=' . urlencode($keyword), false);

        $myListResponse = $this->actingAs($user)->get("/mylist?tab=mylist&keyword={$keyword}");
        $myListResponse->assertStatus(200);
        $myListResponse->assertSee('value="カメラ"', false);
    }
}
