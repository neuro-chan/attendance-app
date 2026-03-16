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
        $users = User::whereIn('email', [
            'testuser1@example.com',
            'reina.n@example.com',
            'tato.y@example.com',
            'issei.y@example.com',
            'keikichi.y@example.com',
            'tomomi.a@example.com',
            'norio.n@example.com',
        ])->get();

        $startDate = Carbon::create(2026, 2, 1);
        $endDate = Carbon::create(2026, 4, 30);

        foreach ($users as $user) {
            $current = $startDate->copy();

            while ($current->lte($endDate)) {
                if (! $current->isWeekend() && ! $current->isWednesday()) {
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
}
