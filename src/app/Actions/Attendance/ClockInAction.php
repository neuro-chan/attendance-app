<?php

namespace App\Actions\Attendance;

use App\Models\Attendance;
use App\Models\User;
use DomainException;

class ClockInAction
{
    public function handle(User $user): Attendance
    {
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->first();

        if ($attendance) {
            throw new DomainException('本日はすでに出勤済みです。');
        }

        return Attendance::create([
            'user_id' => $user->id,
            'work_date' => today(),
            'clock_in' => now(),
        ]);
    }
}
