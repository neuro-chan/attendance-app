<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // ID14: ユーザー情報取得機能（管理者）
    // ========================================
    public function test_管理者ユーザーが全一般ユーザーの氏名とメールアドレスを確認できる(): void
    {
        $admin = User::factory()->admin()->create();
        $user1 = User::factory()->create(['name' => 'テストユーザー1', 'email' => 'user1@example.com']);
        $user2 = User::factory()->create(['name' => 'テストユーザー2', 'email' => 'user2@example.com']);

        $response = $this->actingAs($admin)->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSeeText('テストユーザー1');
        $response->assertSeeText('user1@example.com');
        $response->assertSeeText('テストユーザー2');
        $response->assertSeeText('user2@example.com');
    }

    public function test_ユーザーの勤怠情報が正しく表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.staff.attendance', $user->id));

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        Carbon::setTestNow();
    }

    public function test_前月を押下した時に前月の情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-02-01',
            'clock_in' => Carbon::parse('2026-02-01 09:00:00'),
            'clock_out' => Carbon::parse('2026-02-01 18:00:00'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.staff.attendance', $user->id));
        $response->assertSee(route('admin.staff.attendance', ['id' => $user->id, 'month' => '2026-02']));

        $response = $this->actingAs($admin)->get(route('admin.staff.attendance', ['id' => $user->id, 'month' => '2026-02']));

        $response->assertStatus(200);
        $response->assertSeeText('2026/02');
        $response->assertSee('09:00');

        Carbon::setTestNow();
    }

    public function test_翌月を押下した時に翌月の情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-04-01',
            'clock_in' => Carbon::parse('2026-04-01 09:00:00'),
            'clock_out' => Carbon::parse('2026-04-01 18:00:00'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.staff.attendance', $user->id));
        $response->assertSee(route('admin.staff.attendance', ['id' => $user->id, 'month' => '2026-04']));

        $response = $this->actingAs($admin)->get(route('admin.staff.attendance', ['id' => $user->id, 'month' => '2026-04']));

        $response->assertStatus(200);
        $response->assertSeeText('2026/04');
        $response->assertSee('09:00');

        Carbon::setTestNow();
    }

    public function test_詳細を押下するとその日の勤怠詳細画面に遷移する(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.staff.attendance', $user->id));
        $response->assertSee(route('admin.attendance.show', $attendance->id));

        $response = $this->actingAs($admin)->get(route('admin.attendance.show', $attendance->id));
        $response->assertStatus(200);
    }
}
