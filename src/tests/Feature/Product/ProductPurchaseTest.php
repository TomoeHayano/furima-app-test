<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPurchaseTest extends TestCase
{
  use RefreshDatabase;

  /**
   * 「購入する」ボタン押下で購入が完了し、注文レコードが作成される
   */
  public function test_購入ボタン押下で_購入が完了する(): void
  {
    [$buyer, $product, $shippingData, $response] = $this->completePurchase();

    $response->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('orders', [
      'user_id'                => $buyer->id,
      'product_id'             => $product->id,
      'shipping_postal_code'   => $shippingData['postal_code'],
      'shipping_address'       => $shippingData['address'],
      'shipping_building_name' => $shippingData['building_name'],
    ]);

    $this->assertTrue($product->fresh()->is_sold);
  }

  /**
   * 購入した商品は一覧画面で SOLD 表示になる
   */
  public function test_購入済み商品が_一覧でSOLD表示になる(): void
  {
    [$buyer, $product] = $this->completePurchase();

    $listResponse = $this->get(route('products.index'));

    $listResponse->assertSee($product->name);
    $listResponse->assertSee('SOLD');
  }

  /**
   * 購入した商品がプロフィールの購入一覧に表示される
   */
  public function test_購入済み商品が_プロフィールの購入一覧に表示される(): void
  {
    [$buyer, $product] = $this->completePurchase();

    $profileResponse = $this->actingAs($buyer)->get(route('user.mypage', ['page' => 'buy']));

    $profileResponse->assertStatus(200);
    $profileResponse->assertSee($product->name);
  }

  /**
   * テスト用に購入処理（成功画面）までを完了させる
   *
   * @return array{0: \App\Models\User, 1: \App\Models\Product, 2: array<string, string>, 3: \Illuminate\Testing\TestResponse}
   */
  private function completePurchase(): array
  {
    $seller  = User::factory()->create();
    $product = Product::factory()->create([
      'user_id' => $seller->id,
      'name'    => 'テストカメラ',
      'is_sold' => false,
    ]);

    /** @var User $buyer */
    $buyer = User::factory()->create();
    Profile::factory()->for($buyer)->create([
      'postal_code'   => '123-4567',
      'address'       => '東京都渋谷区テスト1-2-3',
      'building_name' => 'テストビル101',
    ]);

    $shippingData = [
      'postal_code'   => '987-6543',
      'address'       => '東京都新宿区購入者町4-5-6',
      'building_name' => 'サンプルマンション202',
    ];

    $response = $this->actingAs($buyer)
        ->withSession(['shipping_addresses' => [$product->id => $shippingData]])
        ->get(route('purchase.success', $product->id));

    return [$buyer, $product, $shippingData, $response];
  }
}
