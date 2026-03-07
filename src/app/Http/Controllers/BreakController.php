<?php

namespace App\Http\Controllers;

use App\Actions\Attendance\EndBreakAction;
use App\Actions\Attendance\StartBreakAction;
use App\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BreakController extends Controller
{
    // 休憩開始
    public function start(StartBreakAction $action): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->handle($user);

            return redirect()
                ->route('attendance.record')
                ->with('status', '休憩を開始しました。');
        } catch (DomainException $e) {
            return redirect()
                ->route('attendance.record')
                ->withErrors([
                    'attendance' => $e->getMessage(),
                ]);
        }
    }

    // 休憩終了
    public function end(EndBreakAction $action): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $action->handle($user);

            return redirect()
                ->route('attendance.record')
                ->with('status', '休憩を終了しました。');
        } catch (DomainException $e) {
            return redirect()
                ->route('attendance.record')
                ->withErrors([
                    'attendance' => $e->getMessage(),
                ]);
        }
    }
}
