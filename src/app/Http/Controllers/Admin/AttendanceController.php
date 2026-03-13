<?php

namespace App\Http\Controllers\Admin;

use App\Models\BreakTime;
use App\Models\Attendance;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCorrectionRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $currentDate = $request->filled('date')
            ? Carbon::createFromFormat('Y-m-d', $request->string('date'))
            : Carbon::today();

        $attendances = Attendance::with('user')
            ->whereDate('work_date', $currentDate)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendance.index', [
            'attendances'    => $attendances,
            'currentDate'     => $currentDate->format('Y年n月j日'),
            'currentDateNav'  => $currentDate->format('Y/m/d'),
            'previousDayUrl' => route('admin.attendance.index', ['date' => $currentDate->copy()->subDay()->format('Y-m-d')]),
            'nextDayUrl'     => route('admin.attendance.index', ['date' => $currentDate->copy()->addDay()->format('Y-m-d')]),
        ]);
    }

    public function show(int $id): View
    {
        $attendance = Attendance::with('breakTimes')
            ->findOrFail($id);

        $isPending = $attendance->hasPendingRequest();

        return view('admin.attendance.show', [
            'attendance' => $attendance,
            'isPending'  => $isPending,
            'userName'   => $attendance->user->name,
        ]);
    }

    public function update(UserCorrectionRequest $request, int $id): RedirectResponse
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
        $data = $request->validated();

        $attendance->update([
            'clock_in'  => $data['clock_in'],
            'clock_out' => $data['clock_out'],
            'note'      => $data['note'],
        ]);

        foreach ($data['breaks'] ?? [] as $break) {
            if (empty($break['break_start']) && empty($break['break_end'])) {
                continue;
            }

            if (!empty($break['break_id'])) {
                BreakTime::where('id', $break['break_id'])->update([
                    'break_start' => $break['break_start'] ?? null,
                    'break_end'   => $break['break_end'] ?? null,
                ]);
            } else {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start'   => $break['break_start'] ?? null,
                    'break_end'     => $break['break_end'] ?? null,
                ]);
            }
        }

        return redirect()
            ->route('admin.attendance.show', $attendance->id)
            ->with('status', '勤怠情報を更新しました。');
    }

}
