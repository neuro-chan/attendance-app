<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Correction\CorrectionApproveAction;
use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CorrectionApproveController extends Controller
{
    public function approve(int $id): View
    {
        $correction = AttendanceCorrection::with('attendance.breakTimes', 'user')
            ->findOrFail($id);

        return view('admin.request.approve', compact('correction'));
    }

    public function approveStore(int $id, CorrectionApproveAction $action): RedirectResponse
    {
        $correction = AttendanceCorrection::with('attendance', 'breakCorrections')
            ->findOrFail($id);

        /** @var User $approver */
        $approver = Auth::user();

        $action->handle($correction, $approver);

        return redirect()
            ->route('admin.correction.approve', $id)
            ->with('status', '申請を承認しました。');
    }
}
