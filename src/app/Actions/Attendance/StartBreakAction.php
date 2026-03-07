<?php

namespace App\Actions\Attendance;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use DomainException;

class StartBreakAction
{
    public function handle(User $user): BreakTime
    {
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->first();

        if (! $attendance || $attendance->status !== AttendanceStatus::Working) {
            throw new DomainException('出勤中の場合のみ休憩を開始できます。');
        }

        return BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start'   => now(),
        ]);
    }
}
