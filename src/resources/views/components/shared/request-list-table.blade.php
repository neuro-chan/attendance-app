@props([
    'corrections',
    'currentStatus' => 'pending',     // 'pending' | 'approved'
    'pendingUrl' => '#',
    'approvedUrl' => '#',
])

<div class="request-list-tabs">
    <a href="{{ $pendingUrl }}"
       class="request-list-tabs__tab {{ $currentStatus === 'pending' ? 'request-list-tabs__tab--active' : '' }}">
        承認待ち
    </a>

    <a href="{{ $approvedUrl }}"
       class="request-list-tabs__tab {{ $currentStatus === 'approved' ? 'request-list-tabs__tab--active' : '' }}">
        承認済み
    </a>
</div>

<table class="request-list-table">
    <thead class="request-list__head">
        <tr class="request-list__row">
            <th class="request-list__header">状態</th>
            <th class="request-list__header">名前</th>
            <th class="request-list__header">対象日時</th>
            <th class="request-list__header">申請理由</th>
            <th class="request-list__header">申請日時</th>
            <th class="request-list__header">詳細</th>
        </tr>
    </thead>

    <tbody class="request-list__body">
        @foreach ($corrections as $correction)
            <tr class="request-list__row">
                <td class="request-list__data">
                    {{ $correction->status->label() }}
                </td>

                <td class="request-list__data">
                    {{ auth()->user()->name }}
                </td>

                <td class="request-list__data">
                    {{ $correction->attendance->work_date->isoFormat('YYYY/MM/DD') }}
                </td>

                <td class="request-list__data request-list__data--reason">
                    {{ $correction->requested_note }}
                </td>

                <td class="request-list__data">
                    {{ $correction->created_at->format('Y/m/d') }}
                </td>

                <td class="request-list__data">
                    <a href="{{ route('staff.show', $correction->attendance_id) }}"
                       class="request-list__detail-link">詳細</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
