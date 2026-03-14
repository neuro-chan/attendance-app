<?php

namespace App\Actions\Attendance;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FillMonthlyAttendancesAction
{
    public function handle(int $userId, Carbon $currentMonth): Collection
    {
        $attendances = Attendance::with('user')
            ->where('user_id', $userId)
            ->forMonth($currentMonth->year, $currentMonth->month)
            ->orderBy('work_date')
            ->get()
            ->keyBy(fn ($a) => $a->work_date->format('Y-m-d'));

        $days = collect();
        $date = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        while ($date->lte($endOfMonth)) {
            $key = $date->format('Y-m-d');
            $days->push($attendances->get($key) ?? (object) [
                'work_date' => $date->copy(),
                'clock_in' => null,
                'clock_out' => null,
                'id' => null,
                'user' => null,
            ]);
            $date->addDay();
        }

        return $days;
    }
}
