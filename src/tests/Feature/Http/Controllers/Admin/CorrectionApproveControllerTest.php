<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Enums\CorrectionStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorrectionApproveControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // ID15: 勤怠情報修正機能（管理者）
    // ========================================
    public function test_承認待ちの修正申請が全て表示されている(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->admin()->create();
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

        $response = $this->actingAs($admin)->get(route('request.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSeeText('打刻忘れのため');
    }

    public function test_承認済みの修正申請が全て表示されている(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->admin()->create();
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
            'requested_note' => '電車遅延のため',
            'status' => CorrectionStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('request.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertSeeText('電車遅延のため');
    }

    public function test_修正申請の詳細内容が正しく表示されている(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->admin()->create();
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['name' => 'テストユーザー']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => Carbon::parse('2026-03-15 09:30:00'),
            'requested_clock_out' => Carbon::parse('2026-03-15 18:00:00'),
            'requested_note' => '打刻忘れのため',
            'status' => CorrectionStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.correction.approve', $correction->id));

        $response->assertStatus(200);
        $response->assertSeeText('テストユーザー');
        $response->assertSeeText('2026年');
        $response->assertSeeText('3月15日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_修正申請の承認処理が正しく行われる(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->admin()->create();
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => Carbon::parse('2026-03-15 09:30:00'),
            'requested_clock_out' => Carbon::parse('2026-03-15 18:00:00'),
            'requested_note' => '打刻忘れのため',
            'status' => CorrectionStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.correction.approve.store', $correction->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => CorrectionStatus::Approved,
            'approved_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in' => '2026-03-15 09:30:00',
        ]);
    }
}
