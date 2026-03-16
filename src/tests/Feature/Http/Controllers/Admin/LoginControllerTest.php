<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // ID3: ログイン認証機能（管理者）
    // ========================================
    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される(): void
    {
        User::factory()->admin()->create(['email' => 'admin@example.com']);

        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password1234',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_パスワードが未入力の場合バリデーションメッセージが表示される(): void
    {
        User::factory()->admin()->create(['email' => 'admin@example.com']);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_登録内容と一致しない場合バリデーションメッセージが表示される(): void
    {
        User::factory()->admin()->create(['email' => 'admin@example.com']);

        $response = $this->post('/admin/login', [
            'email' => 'notadmin@example.com',
            'password' => 'password1234',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }
}
