@props(['status'])

<div class="attendance-action-buttons">
    {{-- 勤務外：出勤ボタン --}}
    @if (in_array($status, ['off_work', 'offWork', '勤務外'], true))
        <form action="{{ route('attendance.clock-in') }}" method="POST">
            @csrf
            <button type="submit" class="attendance-button attendance-button--primary">
                出勤
            </button>
        </form>

    {{-- 出勤中：退勤・休憩入ボタン --}}
    @elseif (in_array($status, ['working', '出勤中'], true))
        <form action="{{ route('attendance.clock-out') }}" method="POST">
            @csrf
            <button type="submit" class="attendance-button attendance-button--primary">
                退勤
            </button>
        </form>

        <form action="{{ route('attendance.break-start') }}" method="POST">
            @csrf
            <button type="submit" class="attendance-button attendance-button--secondary">
                休憩入
            </button>
        </form>

    {{-- 休憩中：休憩戻ボタン --}}
    @elseif (in_array($status, ['on_break', 'onBreak', '休憩中'], true))
        <form action="{{ route('attendance.break-end') }}" method="POST">
            @csrf
            <button type="submit" class="attendance-button attendance-button--secondary">
                休憩戻
            </button>
        </form>

    {{-- 退勤済：メッセージ表示 --}}
    @elseif (in_array($status, ['finished', '退勤済'], true))
        <p class="attendance-action-buttons__message">
            お疲れ様でした。
        </p>
    @endif
</div>
