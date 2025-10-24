<?php

namespace Tests\Feature\User;

use App\Models\Order;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInfoTest extends TestCase
{
  use RefreshDatabase;

  /**
   * プロフィールページで必要な情報が表示されることを確認する
   */
  public function test_プロフィールページで_必要な情報が表示される(): void
  {
    $user = User::factory()->create([
      'name' => 'プロフィール太郎',
    ]);

    $profile = Profile::factory()->for($user)->create([
      'postal_code' => '150-0000',
      'address'     => '東京都渋谷区テスト1-1-1',
      'image_path'  => 'profile_images/sample.jpg',
    ]);

    $sellingProduct = Product::factory()->create([
      'user_id' => $user->id,
      'name'    => '出品中のテスト商品',
    ]);

    $purchasedProduct = Product::factory()->create([
      'name' => '購入済みのテスト商品',
    ]);

    Order::create([
      'user_id'                => $user->id,
      'product_id'             => $purchasedProduct->id,
      'profile_id'             => $profile->id,
      'payment_method'         => 'カード支払い',
      'shipping_postal_code'   => '123-4567',
      'shipping_address'       => '東京都品川区購入者1-2-3',
      'shipping_building_name' => 'テストマンション101',
    ]);

    $sellResponse = $this->actingAs($user)->get(route('user.mypage', ['page' => 'sell']));

    $sellResponse->assertStatus(200);
    $sellResponse->assertSee($user->name);
    $sellResponse->assertSee(asset('storage/profile_images/sample.jpg'), false);
    $sellResponse->assertSee($sellingProduct->name);
    $sellResponse->assertDontSee($purchasedProduct->name);

    $buyResponse = $this->actingAs($user)->get(route('user.mypage', ['page' => 'buy']));

    $buyResponse->assertStatus(200);
    $buyResponse->assertSee($purchasedProduct->name);
  }
}
