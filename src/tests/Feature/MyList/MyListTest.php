<?php

namespace Tests\Feature\MyList;

use App\Models\Like;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
  use RefreshDatabase;

  /**
   * いいねした商品だけが表示されることを確認する
   */
  public function test_マイリストには_いいねした商品だけが表示される(): void
  {
    $user = User::factory()->create();

    $likedProducts  = Product::factory()->count(2)->create();
    $unlikedProduct = Product::factory()->create();

    foreach ($likedProducts as $product) {
      Like::create([
        'user_id'    => $user->id,
        'product_id' => $product->id,
      ]);
    }

    $response = $this->actingAs($user)->get('/mylist?tab=mylist');

    $response->assertStatus(200);

    foreach ($likedProducts as $product) {
      $response->assertSee($product->name);
    }

    $response->assertDontSee($unlikedProduct->name);
  }

  /**
   * 購入済み商品に SOLD バッジが表示されることを確認する
   */
  public function test_マイリストでは_購入済み商品にSOLDバッジが表示される(): void
  {
    /** @var User $user */
    $user = User::factory()->create();

    $soldProduct = Product::factory()->create([
      'name'    => '売り切れ商品',
      'is_sold' => true,
    ]);

    Like::create([
      'user_id'    => $user->id,
      'product_id' => $soldProduct->id,
    ]);

    $response = $this->actingAs($user)->get('/mylist?tab=recommend');

    $response->assertStatus(200);
    $response->assertSee('SOLD');
  }

  /**
   * 未認証の場合はマイリストに商品が表示されないことを確認する
   */
  public function test_未認証ユーザーは_マイリストに商品が表示されない(): void
  {
    $response = $this->get('/mylist?tab=mylist');

    $response->assertRedirect('/login');

    $this->followingRedirects()
        ->get('/mylist?tab=mylist')
        ->assertDontSee('product-card');
  }
}
