<?php

namespace App\Actions\Attendance;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use DomainException;

class EndBreakAction
{
    public function handle(User $user): BreakTime
    {
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->first();

        if (! $attendance || $attendance->status !== AttendanceStatus::OnBreak) {
            throw new DomainException('休憩中の場合のみ休憩を終了できます。');
        }

        $breakTime = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest('break_start')
            ->first();

        if (! $breakTime) {
            throw new DomainException('終了できる休憩がありません。');
        }

        $breakTime->update([
            'break_end' => now(),
        ]);

        return $breakTime;
    }
}
