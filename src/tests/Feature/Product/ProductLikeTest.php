<?php

namespace Tests\Feature\Product;

use App\Models\Like;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductLikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねアイコンを押下すると、いいねが登録されてカウントが増える
     */
    public function test_いいねアイコンを押下すると_いいねが登録される(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->postJson("/item/{$product->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'liked',
                'likesCount' => 1,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * いいね済みアイコンは押下後に「liked」ステータスが返却される（色変化トリガー）
     */
    public function test_いいね登録後は_レスポンスがlikedステータスを返す(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->postJson("/item/{$product->id}/like");

        $response->assertStatus(200);
        $this->assertEquals('liked', $response->json('status'));
    }

    /**
     * いいね済みの状態で再度押下すると、いいねが解除されカウントが減る
     */
    public function test_再度いいねアイコンを押下すると_いいねが解除される(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->postJson("/item/{$product->id}/like");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'unliked',
                'likesCount' => 0,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}
