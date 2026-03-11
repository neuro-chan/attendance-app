<?php

namespace App\Http\Controllers;

use App\Enums\CorrectionStatus;
use App\Models\AttendanceCorrection;
use App\Actions\Correction\SubmitCorrectionAction;
use App\Http\Requests\UserCorrectionRequest;
use App\Models\Attendance;
use App\Models\User;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;



class AttendanceCorrectionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $corrections = AttendanceCorrection::with('attendance')
            ->where('user_id', Auth::id())
            ->where('status', $status === 'pending' ? CorrectionStatus::Pending : CorrectionStatus::Approved)
            ->latest()
            ->get();

        return view('staff.requests', compact('corrections', 'status'));
    }


    public function store(UserCorrectionRequest $request, int $id, SubmitCorrectionAction $action): RedirectResponse
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->findOrFail($id);

        /** @var User $user */
        $user = Auth::user();

        try {
            $action->handle($attendance, $user, $request->validated());

            return redirect()
                ->route('request.index')
                ->with('status', '修正申請を送信しました。');
        } catch (DomainException $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
