<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Attendance\ExportCsvAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffController extends Controller
{
    public function index(): View
    {
        $users = User::where('role', UserRole::Staff)
            ->orderBy('id')
            ->get();

        return view('admin.staff.show', compact('users'));
    }

    public function show(Request $request, int $id): View
    {
        $currentMonth = $request->filled('month')
            ? Carbon::createFromFormat('Y-m', $request->string('month'))->startOfMonth()
            : Carbon::today()->startOfMonth();

        $attendances = Attendance::where('user_id', $id)
            ->forMonth($currentMonth->year, $currentMonth->month)
            ->orderBy('work_date')
            ->get();

        return view('admin.staff.index', [
            'userName' => User::findOrFail($id)->name,
            'attendances' => $attendances,
            'currentMonth' => $currentMonth->format('Y/m'),
            'staffId' => $id,
            'year' => $currentMonth->year,
            'month' => $currentMonth->month,
            'previousMonthUrl' => route('admin.staff.attendance', ['id' => $id, 'month' => $currentMonth->copy()->subMonth()->format('Y-m')]),
            'nextMonthUrl' => route('admin.staff.attendance', ['id' => $id, 'month' => $currentMonth->copy()->addMonth()->format('Y-m')]),
        ]);
    }

    public function exportCsv(Request $request, int $id, ExportCsvAction $action): StreamedResponse
    {
        $staff = User::findOrFail($id);
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $rows = $action->handle($staff, $year, $month);
        $filename = sprintf('%s_%d%02d.csv', $staff->name, $year, $month);

        return response()->streamDownload(function () use ($rows) {
            $stream = fopen('php://output', 'w');

            fwrite($stream, "\xEF\xBB\xBF");

            foreach ($rows as $row) {
                fputcsv($stream, $row);
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
