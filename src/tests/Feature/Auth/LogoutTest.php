<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

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
        $user = User::factory()->create([
            'email' => 'logouttest@example.com',
            'password' => bcrypt('password123'),
        ]);

        // ユーザーとしてログイン状態にする
        $response = $this->actingAs($user);

        // ログアウトを実行
        $response = $this->post('/logout');

        // ゲスト状態（未ログイン）になっていることを確認
        $this->assertGuest();

        // トップ画面へリダイレクトされることを確認
        $response->assertRedirect('/'); 
    }

    /**
     * ログアウト後、認証が必要なページにアクセスできないことを確認する
     */
    public function test_ログアウト後_認証が必要なページにアクセスできない(): void
    {
        // ログイン状態のユーザーを作成
        $user = User::factory()->create();

        // ログイン状態にする
        $this->actingAs($user);

        // ログアウトを実行
        $this->post('/logout');

        // ログイン後でないとアクセスできないページにアクセス
        $response = $this->get('/mypage');

        // ログイン画面にリダイレクトされることを確認
        $response->assertRedirect('/login');
    }
}
