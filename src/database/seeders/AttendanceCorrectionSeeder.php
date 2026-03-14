<?php

namespace Database\Seeders;

use App\Enums\CorrectionStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceCorrectionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'testuser1@example.com')->first();
        $admin = User::where('email', 'admin@example.com')->first();

        if (! $user) {
            return;
        }

        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('work_date')
            ->get();

        AttendanceCorrection::create([
            'attendance_id' => $attendances[0]->id,
            'user_id' => $user->id,
            'requested_clock_in' => $attendances[0]->work_date->setTime(9, 30),
            'requested_clock_out' => $attendances[0]->work_date->setTime(18, 30),
            'requested_note' => '打刻忘れのため',
            'status' => CorrectionStatus::Pending,
        ]);

        AttendanceCorrection::create([
            'attendance_id' => $attendances[1]->id,
            'user_id' => $user->id,
            'requested_clock_in' => $attendances[1]->work_date->setTime(10, 0),
            'requested_clock_out' => $attendances[1]->work_date->setTime(19, 0),
            'requested_note' => '電車遅延のため',
            'status' => CorrectionStatus::Approved,
            'approved_by' => $admin?->id,
            'approved_at' => now(),
        ]);
    }
}
