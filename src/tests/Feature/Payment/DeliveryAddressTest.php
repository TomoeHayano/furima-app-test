<?php

namespace Tests\Feature\Payment;

use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品毎に保存した配送先住所が購入画面に反映されることを確認する
     */
    public function test_登録した配送先住所が_購入画面に反映される(): void
    {
        [$buyer, $product] = $this->prepareUserAndProduct();

        $shippingData = [
            'postal_code' => '555-6666',
            'address' => '東京都千代田区テスト町7-8-9',
            'building_name' => 'アドレスビル202',
        ];

        $this->actingAs($buyer)
            ->post(route('purchase.address.update', $product->id), $shippingData)
            ->assertRedirect(route('purchase.create', $product->id));

        $response = $this->actingAs($buyer)->get(route('purchase.create', $product->id));

        $response->assertStatus(200);
        $response->assertSee('〒555-6666', false);
        $response->assertSee('東京都千代田区テスト町7-8-9', false);
        $response->assertSee('アドレスビル202', false);
    }

    /**
     * 登録した配送先が注文時に orders テーブルへ紐付いて保存される
     */
    public function test_購入した商品に_配送先住所が紐付いて保存される(): void
    {
        [$buyer, $product] = $this->prepareUserAndProduct();

        $shippingData = [
            'postal_code' => '111-2222',
            'address' => '東京都港区購入者町1-2-3',
            'building_name' => 'サンプルマンション505',
        ];

        $this->actingAs($buyer)
            ->post(route('purchase.address.update', $product->id), $shippingData);

        $this->actingAs($buyer)
            ->get(route('purchase.success', $product->id));

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'shipping_postal_code' => $shippingData['postal_code'],
            'shipping_address' => $shippingData['address'],
            'shipping_building_name' => $shippingData['building_name'],
        ]);
    }

    private function prepareUserAndProduct(): array
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'name' => '配送テスト商品',
        ]);

        $buyer = User::factory()->create();
        Profile::factory()->for($buyer)->create([
            'postal_code' => '999-9999',
            'address' => '東京都渋谷区既定の住所1-1-1',
        ]);

        return [$buyer, $product];
    }
}
