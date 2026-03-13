@props(['attendance', 'userName' => null, 'disabled' => false])

@php
    $userName ??= auth()->user()->name;
    $breaks = $attendance->breakTimes ?? collect();
    $workDate = $attendance->work_date;
    $newIndex = $breaks->count();
@endphp

<table class="attendance-detail-table">
    <tbody class="attendance-detail-table__body">

        {{-- 名前 --}}
        <tr class="attendance-detail-table__row">
            <th class="attendance-detail-table__label">名前</th>
            <td class="attendance-detail-table__value">
                {{ $userName }}
            </td>
        </tr>

        {{-- 日付 --}}
        <tr class="attendance-detail-table__row">
            <th class="attendance-detail-table__label">日付</th>
            <td class="attendance-detail-table__value">
                <div class="attendance-detail-table__date">
                    <span class="attendance-detail-table__dateText">
                        {{ $workDate->format('Y年') }}
                    </span>
                    <span class="attendance-detail-table__spacer"></span>
                    <span class="attendance-detail-table__dateText">
                        {{ $workDate->format('n月j日') }}
                    </span>
                </div>
            </td>
        </tr>

        {{-- 出勤・退勤 --}}
        <tr class="attendance-detail-table__row">
            <th class="attendance-detail-table__label">出勤・退勤</th>
            <td class="attendance-detail-table__value">
                <div class="attendance-detail-table__timeRange">
                    <input class="attendance-detail-table__timeInput" type="text" name="clock_in"
                        value="{{ old('clock_in', optional($attendance->clock_in)->format('H:i')) }}"
                        @disabled($disabled)>
                    <span class="attendance-detail-table__tilde">〜</span>
                    <input class="attendance-detail-table__timeInput" type="text" name="clock_out"
                        value="{{ old('clock_out', optional($attendance->clock_out)->format('H:i')) }}"
                        @disabled($disabled)>
                </div>

                @error('clock_in')
                    <p class="error-text">{{ $message }}</p>
                @enderror
                @error('clock_out')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </td>
        </tr>

        {{-- 既存の休憩 --}}
        @foreach ($breaks as $index => $break)
            <tr class="attendance-detail-table__row">
                <th class="attendance-detail-table__label">
                    {{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}
                </th>
                <td class="attendance-detail-table__value">
                    <input type="hidden" name="breaks[{{ $index }}][break_id]" value="{{ $break->id }}">
                    <div class="attendance-detail-table__timeRange">
                        <input class="attendance-detail-table__timeInput" type="text"
                            name="breaks[{{ $index }}][break_start]"
                            value="{{ old("breaks.$index.break_start", optional($break->break_start)->format('H:i')) }}"
                            @disabled($disabled)>
                        <span class="attendance-detail-table__tilde">〜</span>
                        <input class="attendance-detail-table__timeInput" type="text"
                            name="breaks[{{ $index }}][break_end]"
                            value="{{ old("breaks.$index.break_end", optional($break->break_end)->format('H:i')) }}"
                            @disabled($disabled)>
                    </div>

                    @error("breaks.$index.break_start")
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                    @error("breaks.$index.break_end")
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
        @endforeach

        {{-- 新規追加用の休憩 --}}
        <tr class="attendance-detail-table__row">
            <th class="attendance-detail-table__label">
                {{ $breaks->isEmpty() ? '休憩' : '休憩' . ($newIndex + 1) }}
            </th>
            <td class="attendance-detail-table__value">
                <div class="attendance-detail-table__timeRange">
                    <input class="attendance-detail-table__timeInput" type="text"
                        name="breaks[{{ $newIndex }}][break_start]"
                        value="{{ old("breaks.$newIndex.break_start") }}" @disabled($disabled)>
                    <span class="attendance-detail-table__tilde">〜</span>
                    <input class="attendance-detail-table__timeInput" type="text"
                        name="breaks[{{ $newIndex }}][break_end]" value="{{ old("breaks.$newIndex.break_end") }}"
                        @disabled($disabled)>
                </div>

                @error("breaks.$newIndex.break_start")
                    <p class="error-text">{{ $message }}</p>
                @enderror
                @error("breaks.$newIndex.break_end")
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </td>
        </tr>

        {{-- 備考 --}}
        <tr class="attendance-detail-table__row">
            <th class="attendance-detail-table__label">備考</th>
            <td class="attendance-detail-table__value">
                <textarea class="attendance-detail-table__textarea" name="note" rows="2" @disabled($disabled)>{{ old('note', $attendance->note ?? '') }}</textarea>
                @error('note')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </td>
        </tr>

    </tbody>
</table>
