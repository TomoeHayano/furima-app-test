<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCommentTest extends TestCase
{
  use RefreshDatabase;

  /**
   * ログインユーザーはコメントを送信でき、コメント数が増える
   */
  public function test_ログインユーザーは_コメントを送信できる(): void
  {
    /** @var User $user */
    $user    = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('products.comment.store', $product->id), [
          'content' => 'テストコメント',
        ]);

    $response->assertRedirect(route('products.show', ['itemId' => $product->id]));

    $this->assertDatabaseHas('comments', [
      'user_id'    => $user->id,
      'product_id' => $product->id,
      'content'    => 'テストコメント',
    ]);

    $detailResponse = $this->actingAs($user)->get(route('products.show', ['itemId' => $product->id]));
    $detailResponse->assertSee('コメント (1)');
    $detailResponse->assertSee('テストコメント');
  }

  /**
   * ログインしていないユーザーはコメントを送信できない
   */
  public function test_ゲストユーザーは_コメントを送信できない(): void
  {
    $product = Product::factory()->create();

    $response = $this->post(route('products.comment.store', $product->id), [
      'content' => 'ゲストコメント',
    ]);

    $response->assertRedirect(route('login'));

    $this->assertDatabaseMissing('comments', [
      'product_id' => $product->id,
      'content'    => 'ゲストコメント',
    ]);
  }

  /**
   * コメント未入力の場合はバリデーションエラーが表示される
   */
  public function test_コメント未入力の場合は_バリデーションエラーとなる(): void
  {
    $user    = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)
        ->from(route('products.show', ['itemId' => $product->id]))
        ->post(route('products.comment.store', $product->id), [
          'content' => '',
        ]);

    $response->assertRedirect(route('products.show', ['itemId' => $product->id]));
    $response->assertSessionHasErrors('content');
  }

  /**
   * コメントが255文字を超える場合はバリデーションエラーが表示される
   */
  public function test_コメントが255文字を超えると_バリデーションエラーとなる(): void
  {
    $user        = User::factory()->create();
    $product     = Product::factory()->create();
    $longComment = str_repeat('あ', 256);

    $response = $this->actingAs($user)
        ->from(route('products.show', ['itemId' => $product->id]))
        ->post(route('products.comment.store', $product->id), [
          'content' => $longComment,
        ]);

    $response->assertRedirect(route('products.show', ['itemId' => $product->id]));
    $response->assertSessionHasErrors('content');
  }
}
