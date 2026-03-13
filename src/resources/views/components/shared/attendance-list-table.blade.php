@props(['attendances', 'firstColumn' => 'date', 'routeName' => 'staff.show'])

<table class="attendance-table">
    <thead class="attendance-table__head">
        <tr class="attendance-table__row">
            {{-- firstColumn が 'name' のときはスタッフ名、それ以外は日付 --}}
            <th class="attendance-table__header">{{ $firstColumn === 'name' ? '名前' : '日付' }}</th>
            <th class="attendance-table__header">出勤</th>
            <th class="attendance-table__header">退勤</th>
            <th class="attendance-table__header">休憩</th>
            <th class="attendance-table__header">合計</th>
            <th class="attendance-table__header">詳細</th>
        </tr>
    </thead>

    {{-- 勤怠一覧 --}}
    <tbody class="attendance-table__body">
        @foreach ($attendances as $attendance)
            <tr class="attendance-table__row">
                {{-- 名前 or 日付 --}}
                <td class="attendance-table__data">
                    @if ($firstColumn === 'name')
                        {{ $attendance->user->name }}
                    @else
                        {{ ($attendance->work_date)->isoFormat('MM/DD(ddd)') }}
                    @endif
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
                {{-- 詳細ページへのリンク --}}
                <td class="attendance-table__data">
                    <a href="{{ route($routeName, $attendance->id) }}" class="attendance-table__detail-link">詳細</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
