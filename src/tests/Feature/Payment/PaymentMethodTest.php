<?php

namespace Tests\Feature\Payment;

use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
  use RefreshDatabase;

  /**
   * 支払い方法の選択が購入画面の確認エリアに反映される構造になっていることを確認する
   */
  public function test_支払い方法の選択が_確認エリアに反映される構造になっている(): void
  {
    $seller  = User::factory()->create();
    $product = Product::factory()->create([
      'user_id' => $seller->id,
      'name'    => '支払いテスト商品',
      'price'   => 5000,
    ]);

    /** @var User $buyer */
    $buyer = User::factory()->create();
    Profile::factory()->for($buyer)->create([
      'postal_code' => '123-4567',
      'address'     => '東京都品川区テスト1-2-3',
    ]);

    $response = $this->actingAs($buyer)
        ->withSession([
          "shipping_addresses.{$product->id}" => [
            'postal_code'   => '111-2222',
            'address'       => '東京都中央区購入者町4-5-6',
            'building_name' => 'テストハイツ303',
          ],
        ])
        ->get(route('purchase.create', $product->id));

    $response->assertStatus(200);

    $response->assertSee('<option value="コンビニ支払い">', false);
    $response->assertSee('<option value="カード支払い">', false);

    // 選択した内容が表示される要素が用意され、初期値が「選択してください」であること
    $response->assertSee('<option value="" disabled hidden selected>選択してください</option>', false);
  }
}
