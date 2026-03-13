@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/components/admin/staff-list.css') }}">
@endsection

@section('content')
    <div class="staff-list">
        <h1 class="staff-list__title">スタッフ一覧</h1>

        <table class="staff-table">
            <thead class="staff-table__head">
                <tr class="staff-table__row">
                    <th class="staff-table__header">名前</th>
                    <th class="staff-table__header">メールアドレス</th>
                    <th class="staff-table__header">月次勤怠</th>
                </tr>
            </thead>
            <tbody class="staff-table__body">
                @foreach ($users as $user)
                    <tr class="staff-table__row">
                        <td class="staff-table__data">{{ $user->name }}</td>
                        <td class="staff-table__data">{{ $user->email }}</td>
                        <td class="staff-table__data">
                            <a href="{{ route('admin.staff.attendance', $user->id) }}" class="staff-table__link">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
