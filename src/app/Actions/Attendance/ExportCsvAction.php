<?php

namespace App\Actions\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Carbon\CarbonImmutable;

class ExportCsvAction
{
    public function handle(User $staff, int $year, int $month): array
    {
        $start = CarbonImmutable::create($year, $month, 1)->startOfMonth();
        $end = $start->endOfMonth();

        $attendancesByDate = Attendance::with('breakTimes')
            ->where('user_id', $staff->id)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('work_date')
            ->get()
            ->keyBy(fn (Attendance $a) => $a->work_date->toDateString());

        $rows = [['日付', '出勤', '退勤', '休憩', '合計']];

        for ($d = $start; $d->lte($end); $d = $d->addDay()) {
            /** @var Attendance|null $attendance */
            $attendance = $attendancesByDate->get($d->toDateString());

            $breakMinutes = 0;
            $totalMinutes = null;

            if ($attendance) {
                $breakMinutes = $attendance->breakTimes->sum(function ($break) {
                    if (! $break->break_start || ! $break->break_end) {
                        return 0;
                    }

                    return $break->break_start->diffInMinutes($break->break_end);
                });

                if ($attendance->clock_in && $attendance->clock_out) {
                    $worked = $attendance->clock_in->diffInMinutes($attendance->clock_out);
                    $totalMinutes = max(0, $worked - $breakMinutes);
                }
            }

            $rows[] = [
                $d->format('Y/m/d'),
                $attendance?->clock_in?->format('H:i') ?? '',
                $attendance?->clock_out?->format('H:i') ?? '',
                $breakMinutes ? sprintf('%d:%02d', intdiv($breakMinutes, 60), $breakMinutes % 60) : '',
                $totalMinutes !== null ? sprintf('%d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60) : '',
            ];
        }

        return $rows;
    }
}
