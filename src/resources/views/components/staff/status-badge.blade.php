@props(['status'])

@php
    $label = match ($status) {
        'off_work', 'offWork', '勤務外' => '勤務外',
        'working', '出勤中' => '出勤中',
        'on_break', 'onBreak', '休憩中' => '休憩中',
        'finished', '退勤済' => '退勤済',
        default => '勤務外',
    };
@endphp

<p class="attendance-status-badge">
    {{ $label }}
</p>
