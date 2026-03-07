<?php

namespace App\Actions\Attendance;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\User;
use DomainException;

class ClockOutAction
{
    public function handle(User $user): Attendance
    {
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->first();

        if (! $attendance || $attendance->status !== AttendanceStatus::Working) {
            throw new DomainException('出勤中の場合のみ退勤できます。');
        }

        $attendance->update([
            'clock_out' => now(),
        ]);

        return $attendance;
    }
}
