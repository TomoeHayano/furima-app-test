<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
  use RefreshDatabase;

  /**
   * ログインしていない状態でログアウトを実行した場合、ログイン画面にリダイレクトされる
   */
  public function test_未ログイン状態でログアウトを実行した場合_ログイン画面にリダイレクトされる(): void
  {
    $response = $this->post('/logout');

    $response->assertRedirect('/');
  }

  /**
   * ログイン中のユーザーがログアウトした場合、セッションが破棄されてログイン画面に遷移する
   */
  public function test_ログイン中のユーザーがログアウトした場合_トップページにリダイレクトされる(): void
  {
    // テスト用ユーザーを作成
    /** @var User $user */
    $user = User::factory()->create([
      'email'    => 'logouttest@example.com',
      'password' => bcrypt('password123'),
    ]);


    $response = $this->actingAs($user);
    $response = $this->post('/logout');

    $this->assertGuest();

    $response->assertRedirect('/');
  }

  /**
   * ログアウト後、認証が必要なページにアクセスできないことを確認する
   */
  public function test_ログアウト後_認証が必要なページにアクセスできない(): void
  {
    /** @var User $user */
    $user = User::factory()->create();


    $this->actingAs($user);
    $this->post('/logout');

    $response = $this->get('/mypage');
    $response->assertRedirect('/login');
  }
}
