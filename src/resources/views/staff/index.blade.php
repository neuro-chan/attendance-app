@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/components/shared/nav-calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/shared/attendance-list-table.css') }}">
@endsection

@section('content')
    <div class="attendance-list">
        <h1 class="attendance-list__title">勤怠一覧</h1>

        <x-shared.month-navigation :currentMonth="$currentMonth" :previousMonthUrl="$previousMonthUrl" :nextMonthUrl="$nextMonthUrl" />

        <x-shared.attendance-list-table :attendances="$attendances" />
    </div>
@endsection
