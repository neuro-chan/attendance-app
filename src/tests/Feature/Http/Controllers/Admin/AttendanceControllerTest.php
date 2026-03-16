<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // ID12: 勤怠一覧情報取得機能（管理者）
    // ========================================
    public function test_その日になされた全ユーザーの勤怠情報が正確に確認できる(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $admin = User::factory()->admin()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user1->id,
            'work_date' => today(),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        Attendance::factory()->create([
            'user_id' => $user2->id,
            'work_date' => today(),
            'clock_in' => Carbon::parse('2026-03-15 10:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 19:00:00'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.attendance.index'));

        $response->assertStatus(200);
        $response->assertSeeText($user1->name);
        $response->assertSeeText($user2->name);
        $response->assertSee('09:00');
        $response->assertSee('10:00');

        Carbon::setTestNow();
    }

    public function test_勤怠一覧画面に遷移した際に現在の日付が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.attendance.index'));

        $response->assertStatus(200);
        $response->assertSeeText('2026年3月15日');

        Carbon::setTestNow();
    }

    public function test_前日を押下した時に前の日の勤怠情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-03-14',
            'clock_in' => Carbon::parse('2026-03-14 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-14 18:00:00'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.attendance.index'));
        $response->assertSee(route('admin.attendance.index', ['date' => '2026-03-14']));

        $response = $this->actingAs($admin)->get(route('admin.attendance.index', ['date' => '2026-03-14']));

        $response->assertStatus(200);
        $response->assertSeeText('2026年3月14日');
        $response->assertSeeText($user->name);

        Carbon::setTestNow();
    }

    public function test_翌日を押下した時に次の日の勤怠情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-03-16',
            'clock_in' => Carbon::parse('2026-03-16 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-16 18:00:00'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.attendance.index'));
        $response->assertSee(route('admin.attendance.index', ['date' => '2026-03-16']));

        $response = $this->actingAs($admin)->get(route('admin.attendance.index', ['date' => '2026-03-16']));

        $response->assertStatus(200);
        $response->assertSeeText('2026年3月16日');
        $response->assertSeeText($user->name);

        Carbon::setTestNow();
    }

    // ========================================
    // ID13: 勤怠詳細情報取得・修正機能（管理者）
    // ========================================
    public function test_勤怠詳細画面に表示されるデータが選択したものになっている(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['name' => 'テストユーザー']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.attendance.show', $attendance->id));

        $response->assertStatus(200);
        $response->assertSeeText('テストユーザー');
        $response->assertSeeText('2026年');
        $response->assertSeeText('3月15日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_出勤時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($admin)->get(route('admin.attendance.show', $attendance->id));

        $response = $this->actingAs($admin)->post(route('admin.attendance.update', $attendance->id), [
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'note' => 'テスト',
            'breaks' => [],
        ]);

        $response->assertSessionHasErrors(['clock_in' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_休憩開始時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($admin)->get(route('admin.attendance.show', $attendance->id));

        $response = $this->actingAs($admin)->post(route('admin.attendance.update', $attendance->id), [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => 'テスト',
            'breaks' => [
                ['break_start' => '19:00', 'break_end' => '20:00'],
            ],
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_start' => '休憩時間が不適切な値です']);
    }

    public function test_休憩終了時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($admin)->get(route('admin.attendance.show', $attendance->id));

        $response = $this->actingAs($admin)->post(route('admin.attendance.update', $attendance->id), [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => 'テスト',
            'breaks' => [
                ['break_start' => '12:00', 'break_end' => '19:00'],
            ],
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_end' => '休憩時間もしくは退勤時間が不適切な値です']);
    }

    public function test_備考欄が未入力の場合エラーメッセージが表示される(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($admin)->get(route('admin.attendance.show', $attendance->id));

        $response = $this->actingAs($admin)->post(route('admin.attendance.update', $attendance->id), [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => '',
            'breaks' => [],
        ]);

        $response->assertSessionHasErrors(['note' => '備考を記入してください']);
    }
}
