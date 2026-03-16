<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\CorrectionStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCorrectionControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // ID11: 勤怠詳細情報修正機能（一般ユーザー）
    // ========================================
    public function test_出勤時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($user)->get(route('staff.show', $attendance->id));

        $response = $this->actingAs($user)->post(route('correction.store', $attendance->id), [
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'note' => 'テスト',
            'breaks' => [],
        ]);

        $response->assertSessionHasErrors(['clock_in' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_休憩開始時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($user)->get(route('staff.show', $attendance->id));

        $response = $this->actingAs($user)->post(route('correction.store', $attendance->id), [
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
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($user)->get(route('staff.show', $attendance->id));

        $response = $this->actingAs($user)->post(route('correction.store', $attendance->id), [
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
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($user)->get(route('staff.show', $attendance->id));

        $response = $this->actingAs($user)->post(route('correction.store', $attendance->id), [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => '',
            'breaks' => [],
        ]);

        $response->assertSessionHasErrors(['note' => '備考を記入してください']);
    }

    public function test_修正申請処理が実行される(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $response = $this->actingAs($user)->post(route('correction.store', $attendance->id), [
            'clock_in' => '09:30',
            'clock_out' => '18:00',
            'note' => '打刻忘れのため',
            'breaks' => [],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_corrections', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_承認待ちにログインユーザーが行った申請が全て表示されている(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $this->actingAs($user)->post(route('correction.store', $attendance->id), [
            'clock_in' => '09:30',
            'clock_out' => '18:00',
            'note' => '打刻忘れのため',
            'breaks' => [],
        ]);

        $response = $this->actingAs($user)->get(route('request.index'));

        $response->assertStatus(200);
        $response->assertSeeText('打刻忘れのため');
    }

    public function test_承認済みに管理者が承認した修正申請が全て表示されている(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        /** @var \App\Models\User $admin */
        $admin = User::factory()->admin()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => Carbon::parse('2026-03-15 09:30:00'),
            'requested_clock_out' => Carbon::parse('2026-03-15 18:00:00'),
            'requested_note' => '打刻忘れのため',
            'status' => CorrectionStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('request.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertSeeText('打刻忘れのため');
    }

    public function test_各申請の詳細を押下すると勤怠詳細画面に遷移する(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => Carbon::parse('2026-03-15 09:30:00'),
            'requested_clock_out' => Carbon::parse('2026-03-15 18:00:00'),
            'requested_note' => '打刻忘れのため',
            'status' => CorrectionStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get(route('request.index'));
        // 4. 「詳細」ボタンのリンク先を確認する
        $response->assertSee(route('staff.show', $attendance->id));

        $response = $this->actingAs($user)->get(route('staff.show', $attendance->id));
        $response->assertStatus(200);
    }
}
