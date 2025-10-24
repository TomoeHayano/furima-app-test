<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
  use RefreshDatabase;

  /**
   * 名前が入力されていない場合、バリデーションメッセージが表示される
   */
  public function test_名前が入力されていない場合_バリデーションメッセージが表示される(): void
  {
    $response = $this->post('/register', [
      'name'                  => '',
      'email'                 => 'test@example.com',
      'password'              => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
  }

  /**
   * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
   */
  public function test_メールアドレスが入力されていない場合_バリデーションメッセージが表示される(): void
  {
    $response = $this->post('/register', [
      'name'                  => 'テストユーザー',
      'email'                 => '',
      'password'              => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
  }

  /**
   * パスワードが入力されていない場合、バリデーションメッセージが表示される
   */
  public function test_パスワードが入力されていない場合_バリデーションメッセージが表示される(): void
  {
    $response = $this->post('/register', [
      'name'                  => 'テストユーザー',
      'email'                 => 'test@example.com',
      'password'              => '',
      'password_confirmation' => '',
    ]);

    $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
  }

  /**
   * パスワードが7文字以下の場合、バリデーションメッセージが表示される
   */
  public function test_パスワードが7文字以下の場合_バリデーションメッセージが表示される(): void
  {
    $response = $this->post('/register', [
      'name'                  => 'テストユーザー',
      'email'                 => 'test@example.com',
      'password'              => 'pass123',
      'password_confirmation' => 'pass123',
    ]);

    $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
  }

  /**
   * パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
   */
  public function test_パスワードが確認用パスワードと一致しない場合_バリデーションメッセージが表示される(): void
  {
    $response = $this->post('/register', [
      'name'                  => 'テストユーザー',
      'email'                 => 'test@example.com',
      'password'              => 'password123',
      'password_confirmation' => 'different123',
    ]);

    $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
  }

  /**
   * 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移する
   */
  public function test_全ての項目が入力されている場合_プロフィール設定画面に遷移する(): void
  {
    $response = $this->post('/register', [
      'name'                  => 'テストユーザー',
      'email'                 => 'test@example.com',
      'password'              => 'password123',
      'password_confirmation' => 'password123',
    ]);

    // 登録されたユーザーがDBに存在することを確認
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

    // Fortifyの仕様により、初回登録後はメール認証画面へ遷移する
    $response->assertRedirect('/email/verify');
  }
}
