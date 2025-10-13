<?php

namespace Tests\Feature\User;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * プロフィール編集画面に既存のユーザー情報が初期値として表示される
     */
    public function test_プロフィール編集画面に_既存の情報が初期値として表示される(): void
    {
        $user = User::factory()->create([
            'name' => '初期ユーザー名',
        ]);

        Profile::factory()->for($user)->create([
            'postal_code' => '123-4567',
            'address' => '東京都足立区初期住所1-2-3',
            'building_name' => '初期ビル404',
            'image_path' => 'profile_images/initial.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('user.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('value="初期ユーザー名"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="東京都足立区初期住所1-2-3"', false);
        $response->assertSee('value="初期ビル404"', false);
        $response->assertSee('profile_images/initial.jpg');
    }
}
