<?php

namespace Tests\Feature\Product;

use App\Models\Order;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
  use RefreshDatabase;

  /**
   * 全ての商品が表示されることを確認する
   */
  public function test_商品ページを開くと_全ての商品が表示される(): void
  {
    // 商品を3件作成
    $products = Product::factory()->count(3)->create();

    // 商品一覧ページにアクセス
    $response = $this->get('/');

    $response->assertStatus(200);

    // 全ての商品名が表示されていることを確認
    foreach ($products as $product) {
      $response->assertSee($product->name);
    }
  }

  /**
   * 購入済み商品に「Sold」ラベルが表示されることを確認する
   */
  public function test_購入済み商品には_Soldラベルが表示される(): void
  {
    // 出品者と購入者を作成
    $seller = User::factory()->create();
    $buyer  = User::factory()->create();

    // 出品者の商品を作成
    $product = Product::factory()->create([
      'user_id' => $seller->id,
      'name'    => 'テスト商品',
      'is_sold' => true,
    ]);

    // 購入者のプロフィールを作成（orders.profile_id の外部キーを満たす）
    $buyerProfile = Profile::factory()->create([
      'id'      => $buyer->id,
      'user_id' => $buyer->id,
    ]);

    // 該当商品を orders テーブルに登録（購入済み状態）
    Order::factory()->create([
      'product_id'     => $product->id,
      'user_id'        => $buyer->id,
      'profile_id'     => $buyerProfile->id,
      'payment_method' => 'credit',
    ]);

    // 商品の最新状態を反映
    $product->refresh();

    // 一覧ページを表示
    $response = $this->get('/');

    // SOLD バッジが表示されていることを確認
    $response->assertSee('SOLD');
  }

  /**
   * 自分が出品した商品が一覧に表示されないことを確認する
   */
  public function test_自分が出品した商品は_一覧に表示されない(): void
  {
    // ログインユーザーを作成
    $user = User::factory()->create();

    // 自分が出品した商品
    $ownProduct = Product::factory()->create([
      'user_id' => $user->id,
      'name'    => '自分の商品',
    ]);

    // 他人が出品した商品
    $otherProduct = Product::factory()->create([
      'name' => '他人の商品',
    ]);

    // ログイン状態で商品一覧ページを開く
    $response = $this->actingAs($user)->get('/');

    // 自分の商品は表示されない
    $response->assertDontSee($ownProduct->name);

    // 他人の商品は表示される
    $response->assertSee($otherProduct->name);
  }
}
