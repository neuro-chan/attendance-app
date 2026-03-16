<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // ID4: 日時取得機能
    // ========================================
    public function test_現在の日時情報が_u_iと同じ形式で出力されている(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('attendance.record'));

        $response->assertStatus(200);
        $response->assertSeeText('2026年3月15日');
        $response->assertSeeText('09:00');

        Carbon::setTestNow();
    }

    // ========================================
    // ID5: ステータス確認機能
    // ========================================
    public function test_勤務外の場合勤怠ステータスが正しく表示される(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('attendance.record'));

        $response->assertStatus(200);
        $response->assertSeeText('勤務外');
    }

    public function test_出勤中の場合勤怠ステータスが正しく表示される(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.record'));

        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    public function test_休憩中の場合勤怠ステータスが正しく表示される(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
            'break_end' => null,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.record'));

        $response->assertStatus(200);
        $response->assertSeeText('休憩中');
    }

    public function test_退勤済の場合勤怠ステータスが正しく表示される(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(8),
            'clock_out' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('attendance.record'));

        $response->assertStatus(200);
        $response->assertSeeText('退勤済');
    }

    // ========================================
    // ID6: 出勤機能
    // ========================================
    public function test_出勤ボタンが正しく機能する(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('出勤');

        $postResponse = $this->actingAs($user)->post(route('attendance.clock-in'));
        $postResponse->assertRedirect();

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('出勤中');

        Carbon::setTestNow();
    }

    public function test_出勤は一日一回のみできる(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(8),
            'clock_out' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertDontSeeText('出勤');

        Carbon::setTestNow();
    }

    public function test_出勤時刻が勤怠一覧画面で確認できる(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $postResponse = $this->actingAs($user)->post(route('attendance.clock-in'));
        $postResponse->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'clock_in' => '2026-03-15 09:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('staff.index'));
        $response->assertStatus(200);
        $response->assertSeeText('09:00');

        Carbon::setTestNow();
    }

    // ========================================
    // ID7: 休憩機能
    // ========================================
    public function test_休憩ボタンが正しく機能する(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('休憩入');

        $postResponse = $this->actingAs($user)->post(route('attendance.break-start'));
        $postResponse->assertRedirect();

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('休憩中');

        Carbon::setTestNow();
    }

    public function test_休憩は一日に何回でもできる(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        $this->actingAs($user)->post(route('attendance.break-start'));
        $this->actingAs($user)->post(route('attendance.break-end'));

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('休憩入');

        Carbon::setTestNow();
    }

    public function test_休憩戻ボタンが正しく機能する(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
            'break_end' => null,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('休憩戻');

        $postResponse = $this->actingAs($user)->post(route('attendance.break-end'));
        $postResponse->assertRedirect();

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('出勤中');

        Carbon::setTestNow();
    }

    public function test_休憩戻は一日に何回でもできる(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        $this->actingAs($user)->post(route('attendance.break-start'));
        $this->actingAs($user)->post(route('attendance.break-end'));
        $this->actingAs($user)->post(route('attendance.break-start'));

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('休憩戻');

        Carbon::setTestNow();
    }

    public function test_休憩時刻が勤怠一覧画面で確認できる(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 12:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(3),
            'clock_out' => null,
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
            'break_end' => now()->addHour(),
        ]);

        $this->assertDatabaseHas('breaktimes', [
            'attendance_id' => $attendance->id,
            'break_start' => '2026-03-15 12:00:00',
            'break_end' => '2026-03-15 13:00:00',
        ]);

        Carbon::setTestNow();
    }

    // ========================================
    // ID8: 退勤機能
    // ========================================
    public function test_退勤ボタンが正しく機能する(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 18:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(8),
            'clock_out' => null,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('退勤');

        $postResponse = $this->actingAs($user)->post(route('attendance.clock-out'));
        $postResponse->assertRedirect();

        $response = $this->actingAs($user)->get(route('attendance.record'));
        $response->assertSeeText('退勤済');

        Carbon::setTestNow();
    }

    public function test_退勤時刻が勤怠一覧画面で確認できる(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 18:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now()->subHours(8),
            'clock_out' => null,
        ]);

        $postResponse = $this->actingAs($user)->post(route('attendance.clock-out'));
        $postResponse->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'clock_out' => '2026-03-15 18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('staff.index'));
        $response->assertStatus(200);
        $response->assertSeeText('18:00');

        Carbon::setTestNow();
    }

    // ========================================
    // ID9: 勤怠一覧情報取得機能（一般ユーザー）
    // ========================================
    public function test_自分が行った勤怠情報が全て表示されている(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-03-01',
            'clock_in' => '2026-03-01 09:00:00',
            'clock_out' => '2026-03-01 18:00:00',
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-03-02',
            'clock_in' => '2026-03-02 09:00:00',
            'clock_out' => '2026-03-02 18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('staff.index'));

        $response->assertStatus(200);
        $response->assertSeeText('03/01');
        $response->assertSeeText('03/02');

        Carbon::setTestNow();
    }

    public function test_勤怠一覧画面に遷移した際に現在の月が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('staff.index'));

        $response->assertStatus(200);
        $response->assertSeeText('2026/03');

        Carbon::setTestNow();
    }

    public function test_前月を押下した時に前月の情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-02-01',
            'clock_in' => '2026-02-01 09:00:00',
            'clock_out' => '2026-02-01 18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('staff.index'));
        $response->assertSee(route('staff.index', ['month' => '2026-02']));

        $response = $this->actingAs($user)->get(route('staff.index', ['month' => '2026-02']));

        $response->assertStatus(200);
        $response->assertSeeText('2026/02');
        $response->assertSeeText('02/01');

        Carbon::setTestNow();
    }

    public function test_翌月を押下した時に翌月の情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-04-01',
            'clock_in' => '2026-04-01 09:00:00',
            'clock_out' => '2026-04-01 18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('staff.index'));
        $response->assertSee(route('staff.index', ['month' => '2026-04']));

        $response = $this->actingAs($user)->get(route('staff.index', ['month' => '2026-04']));

        $response->assertStatus(200);
        $response->assertSeeText('2026/04');
        $response->assertSeeText('04/01');

        Carbon::setTestNow();
    }

    public function test_詳細を押下するとその日の勤怠詳細画面に遷移する(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 09:00:00'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-03-01',
            'clock_in' => '2026-03-01 09:00:00',
            'clock_out' => '2026-03-01 18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('staff.index'));
        $response->assertSee(route('staff.show', $attendance->id));

        $response = $this->actingAs($user)->get(route('staff.show', $attendance->id));
        $response->assertStatus(200);

        Carbon::setTestNow();
    }

    // ========================================
    // ID10: 勤怠詳細情報取得機能（一般ユーザー）
    // ========================================
    public function test_勤怠詳細画面の名前がログインユーザーの氏名になっている(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['name' => 'テストユーザー']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-03-15',
            'clock_in' => '2026-03-15 09:00:00',
            'clock_out' => '2026-03-15 18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('staff.show', $attendance->id));

        $response->assertStatus(200);
        $response->assertSeeText('テストユーザー');
    }

    public function test_勤怠詳細画面の日付が選択した日付になっている(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-03-15',
            'clock_in' => '2026-03-15 09:00:00',
            'clock_out' => '2026-03-15 18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('staff.show', $attendance->id));

        $response->assertStatus(200);
        $response->assertSeeText('2026年');
        $response->assertSeeText('3月15日');
    }

    public function test_出勤退勤にて記されている時間がログインユーザーの打刻と一致している(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        $response = $this->actingAs($user)->get(route('staff.show', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_休憩にて記されている時間がログインユーザーの打刻と一致している(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::parse('2026-03-15'),
            'clock_in' => Carbon::parse('2026-03-15 09:00:00'),
            'clock_out' => Carbon::parse('2026-03-15 18:00:00'),
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::parse('2026-03-15 12:00:00'),
            'break_end' => Carbon::parse('2026-03-15 13:00:00'),
        ]);

        $response = $this->actingAs($user)->get(route('staff.show', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
