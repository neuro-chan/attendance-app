<table class="attendance-table">
    <thead class="attendance-table__head">
        <tr class="attendance-table__row">
            <th class="attendance-table__header">日付</th>
            <th class="attendance-table__header">出勤</th>
            <th class="attendance-table__header">退勤</th>
            <th class="attendance-table__header">休憩</th>
            <th class="attendance-table__header">合計</th>
            <th class="attendance-table__header">詳細</th>
        </tr>
    </thead>
    <tbody class="attendance-table__body">
        @foreach ($attendances as $attendance)
            <tr class="attendance-table__row">
                <td class="attendance-table__data">
                    {{ ($attendance->work_date)->isoFormat('MM/DD(ddd)') }}
                </td>
                <td class="attendance-table__data">
                    {{ optional($attendance->clock_in)->format('H:i') }}
                </td>
                <td class="attendance-table__data">
                    {{ optional($attendance->clock_out)->format('H:i') }}
                </td>
                <td class="attendance-table__data">
                    1:00
                </td>
                <td class="attendance-table__data">
                    8:00
                </td>
                <td class="attendance-table__data">
                    <a href="{{ route('staff.show', $attendance->id) }}" class="attendance-table__detail-link">詳細</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
