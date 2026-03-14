<?php

namespace App\Http\Controllers;

use App\Actions\Attendance\ClockInAction;
use App\Actions\Attendance\ClockOutAction;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    // 勤怠一覧（月単位）
    public function index(Request $request): View
    {
        $currentMonth = $request->filled('month')
            ? Carbon::createFromFormat('Y-m', $request->string('month'))->startOfMonth()
            : Carbon::today()->startOfMonth();

        $attendances = Attendance::where('user_id', Auth::id())
            ->forMonth($currentMonth->year, $currentMonth->month)
            ->orderBy('work_date')
            ->get();

        return view('staff.index', [
            'attendances' => $attendances,
            'currentMonth' => $currentMonth->format('Y/m'),
            'previousMonthUrl' => route('staff.index', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]),
            'nextMonthUrl' => route('staff.index', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]),
        ]);
    }

    // 打刻画面
    public function record(): View
    {
        $attendance = Attendance::firstOrNew([
            'user_id' => Auth::id(),
            'work_date' => today(),
        ]);

        $status = $attendance->status->value;
        $now = now();

        return view('staff.record', compact('attendance', 'status', 'now'));
    }

    // 勤怠詳細
    public function show(int $id): View
    {
        // 自分の勤怠のみ参照可能
        $attendance = Attendance::with('breakTimes')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $isPending = $attendance->hasPendingRequest();

        return view('staff.show', compact('attendance', 'isPending'));
    }

    // 出勤打刻
    public function clockIn(ClockInAction $action): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->handle($user);

            return redirect()
                ->route('attendance.record')
                ->with('status', '出勤を登録しました。');
        } catch (DomainException $e) {
            return redirect()
                ->route('attendance.record')
                ->withErrors(['attendance' => $e->getMessage()]);
        }
    }

    // 退勤打刻
    public function clockOut(ClockOutAction $action): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->handle($user);

            return redirect()
                ->route('attendance.record')
                ->with('status', '退勤を登録しました。');
        } catch (DomainException $e) {
            return redirect()
                ->route('attendance.record')
                ->withErrors(['attendance' => $e->getMessage()]);
        }
    }
}
