<?php

namespace App\Http\Controllers;

use App\Actions\Correction\SubmitCorrectionAction;
use App\Http\Requests\UserCorrectionRequest;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceCorrectionController extends Controller
{
    // 申請一覧（管理者・スタッフで表示を出し分け）
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        /** @var User $user */
        $user = Auth::user();

        // 管理者：全スタッフの申請を表示
        if ($user->isAdmin()) {
            $corrections = AttendanceCorrection::with('attendance', 'user')
                ->when($status === 'pending', fn ($q) => $q->pending())
                ->when($status === 'approved', fn ($q) => $q->approved())
                ->latest()
                ->get();

            return view('admin.request.index', compact('corrections', 'status'));
        }

        // スタッフ：自分の申請のみ表示
        $corrections = AttendanceCorrection::with('attendance')
            ->where('user_id', $user->id)
            ->when($status === 'pending', fn ($q) => $q->pending())
            ->when($status === 'approved', fn ($q) => $q->approved())
            ->latest()
            ->get();

        return view('staff.requests', compact('corrections', 'status'));
    }

    // 修正申請の新規作成（スタッフ）
    public function store(UserCorrectionRequest $request, int $id, SubmitCorrectionAction $action): RedirectResponse
    {
        // 自分の勤怠のみ申請可能
        $attendance = Attendance::where('user_id', Auth::id())
            ->findOrFail($id);

        /** @var User $user */
        $user = Auth::user();

        try {
            $action->handle($attendance, $user, $request->validated());

            return redirect()
                ->route('request.index')
                ->with('status', '申請を承認しました。');
        } catch (DomainException $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
