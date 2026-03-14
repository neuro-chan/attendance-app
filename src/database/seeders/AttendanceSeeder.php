<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user1@example.com')->first();

        if (! $user) {
            return;
        }

        $startDate = Carbon::create(2026, 2, 1);
        $endDate = Carbon::create(2026, 4, 30);

        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if (! $current->isWeekend()) {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'work_date' => $current->toDateString(),
                    'clock_in' => $current->copy()->setTime(9, 0),
                    'clock_out' => $current->copy()->setTime(18, 0),
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $current->copy()->setTime(12, 0),
                    'break_end' => $current->copy()->setTime(13, 0),
                ]);
            }

            $current->addDay();
        }
    }
}
