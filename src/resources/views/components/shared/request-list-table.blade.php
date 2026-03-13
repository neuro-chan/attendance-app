@props(['corrections', 'currentStatus' => 'pending', 'pendingUrl' => '#', 'approvedUrl' => '#', 'isAdmin' => false])

{{-- タブ --}}
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

{{-- 申請一覧テーブル --}}
<table class="request-list-table">
    {{-- ヘッダー --}}
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

    {{-- 申請一覧 --}}
    <tbody class="request-list__body">
        @foreach ($corrections as $correction)
            <tr class="request-list__row">
                <td class="request-list__data">
                    {{ $correction->status->label() }}
                </td>

                {{-- 管理者はスタッフ名、スタッフは自分の名前 --}}
                <td class="request-list__data">
                    {{ $isAdmin ? $correction->user->name : auth()->user()->name }}
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

                {{-- 管理者は承認ページ、スタッフは詳細ページへ --}}
                <td class="request-list__data">
                    <a href="{{ $isAdmin ? route('admin.correction.approve', $correction->id) : route('staff.show', $correction->attendance_id) }}"
                        class="request-list__detail-link">詳細</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
