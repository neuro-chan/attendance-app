@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/components/shared/nav-calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/shared/attendance-list-table.css') }}">
@endsection

@section('content')
    <div class="attendance-list">
        <h1 class="attendance-list__title">{{ $userName }}さんの勤怠</h1>

        <x-shared.month-navigation :currentMonth="$currentMonth" :previousMonthUrl="$previousMonthUrl" :nextMonthUrl="$nextMonthUrl" />

        <x-shared.attendance-list-table :attendances="$attendances" routeName="admin.attendance.show" />
        <div class="attendance-list__actions">
            <a href="{{ route('admin.staff.export', ['id' => $staffId, 'year' => $year, 'month' => $month]) }}"
                class="attendance-list__button--export">
                CSV出力
            </a>
        </div>
    </div>
@endsection
