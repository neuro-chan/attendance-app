<?php

namespace App\Actions\Correction;

use App\Enums\CorrectionStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\BreakCorrection;
use App\Models\User;

class SubmitCorrectionAction
{
    public function handle(Attendance $attendance, User $user, array $data): AttendanceCorrection
    {
        // 1. attendance_corrections に1件作成
        $correction = AttendanceCorrection::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $user->id,
            'requested_clock_in'  => $data['clock_in'],
            'requested_clock_out' => $data['clock_out'],
            'requested_note'      => $data['note'],
            'status'              => CorrectionStatus::Pending,
        ]);

        // 2. break_corrections に休憩行数分作成
        foreach ($data['breaks'] ?? [] as $break) {
            // 両方空なら無視
            if (empty($break['break_start']) && empty($break['break_end'])) {
                continue;
            }

            BreakCorrection::create([
                'correction_id'          => $correction->id,
                'break_id'               => $break['break_id'] ?? null,
                'requested_break_start'  => $break['break_start'] ?? null,
                'requested_break_end'    => $break['break_end'] ?? null,
            ]);
        }

        return $correction;
    }
}
