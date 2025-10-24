<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
  use RefreshDatabase;

  /**
   * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
   */
  public function test_メールアドレスが入力されていない場合_バリデーションメッセージが表示される(): void
  {
    $response = $this->post('/login', [
      'email'    => '',
      'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
  }

  /**
   * パスワードが入力されていない場合、バリデーションメッセージが表示される
   */
  public function test_パスワードが入力されていない場合_バリデーションメッセージが表示される(): void
  {
    $response = $this->post('/login', [
      'email'    => 'test@example.com',
      'password' => '',
    ]);

    $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
  }

  /**
   * 入力情報が間違っている場合、バリデーションメッセージが表示される
   */
  public function test_入力情報が間違っている場合_バリデーションメッセージが表示される(): void
  {
    // 登録されていない情報でログインを試行
    $response = $this->from('/login')->post('/login', [
      'email'    => 'notfound@example.com',
      'password' => 'wrongpassword',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
  }

  /**
   * 正しい情報が入力された場合、ログイン処理が実行される
   */
  public function test_正しい情報が入力された場合_ログイン処理が実行される(): void
  {
    // 事前にユーザーを作成
    $user = User::factory()->create([
      'email'             => 'loginuser@example.com',
      'password'          => Hash::make('password123'),
      'email_verified_at' => now(),
    ]);

    // ログイン実行
    $response = $this->post('/login', [
      'email'    => 'loginuser@example.com',
      'password' => 'password123',
    ]);

    // ログイン成功後、トップページにリダイレクトされる想定
    $response->assertRedirect(route('products.mylist', ['tab' => 'mylist']));
    $this->assertAuthenticatedAs($user);
  }
}
