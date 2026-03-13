<?php

namespace App\Actions\Correction;

use App\Enums\CorrectionStatus;
use App\Models\AttendanceCorrection;
use App\Models\BreakTime;
use App\Models\User;

class CorrectionApproveAction
{
    public function handle(AttendanceCorrection $correction, User $approver): void
    {
        $correction->attendance->update([
            'clock_in'  => $correction->requested_clock_in,
            'clock_out' => $correction->requested_clock_out,
            'note'      => $correction->requested_note,
        ]);

        foreach ($correction->breakCorrections as $breakCorrection) {
            if ($breakCorrection->break_id) {
                BreakTime::where('id', $breakCorrection->break_id)->update([
                    'break_start' => $breakCorrection->requested_break_start,
                    'break_end'   => $breakCorrection->requested_break_end,
                ]);
            } else {
                BreakTime::create([
                    'attendance_id' => $correction->attendance_id,
                    'break_start'   => $breakCorrection->requested_break_start,
                    'break_end'     => $breakCorrection->requested_break_end,
                ]);
            }
        }

        $correction->update([
            'status'      => CorrectionStatus::Approved,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }
}
