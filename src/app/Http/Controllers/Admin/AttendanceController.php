<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DateTimeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCorrectionRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    // 勤怠一覧（日単位）
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
            'attendances' => $attendances,
            'currentDate' => $currentDate->format('Y年n月j日'),
            'currentDateNav' => $currentDate->format('Y/m/d'),
            'previousDayUrl' => route('admin.attendance.index', ['date' => $currentDate->copy()->subDay()->format('Y-m-d')]),
            'nextDayUrl' => route('admin.attendance.index', ['date' => $currentDate->copy()->addDay()->format('Y-m-d')]),
        ]);
    }

    // 勤怠詳細
    public function show(int $id): View
    {
        $attendance = Attendance::with('breakTimes')
            ->findOrFail($id);

        $isPending = $attendance->hasPendingRequest();

        return view('admin.attendance.show', [
            'attendance' => $attendance,
            'isPending' => $isPending,
            'userName' => $attendance->user->name,
        ]);
    }

    // 勤怠情報の直接編集
    public function update(UserCorrectionRequest $request, int $id): RedirectResponse
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
        $data = $request->validated();

        // 勤怠情報を上書き
        $attendance->update([
            'clock_in' => $data['clock_in'],
            'clock_out' => $data['clock_out'],
            'note' => $data['note'],
        ]);

        foreach ($data['breaks'] ?? [] as $break) {
            if (empty($break['break_start']) && empty($break['break_end'])) {
                continue;
            }

            if (! empty($break['break_id'])) {
                // 既存休憩の更新
                BreakTime::findOrFail($break['break_id'])->update([
                    'break_start' => DateTimeHelper::combineDateAndTime($attendance->work_date, $break['break_start']),
                    'break_end' => DateTimeHelper::combineDateAndTime($attendance->work_date, $break['break_end']),
                ]);
            } else {
                // 新規休憩の追加
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => DateTimeHelper::combineDateAndTime($attendance->work_date, $break['break_start']),
                    'break_end' => DateTimeHelper::combineDateAndTime($attendance->work_date, $break['break_end']),
                ]);
            }
        }

        return redirect()
            ->route('admin.attendance.show', $attendance->id)
            ->with('status', '勤怠情報を更新しました。');
    }
}
