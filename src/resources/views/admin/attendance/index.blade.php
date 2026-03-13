@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/components/shared/nav-calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/shared/attendance-list-table.css') }}">
@endsection

@section('content')
    <div class="attendance-list">
        <h1 class="attendance-list__title">{{ $currentDate }}の勤怠</h1>

        <x-shared.daily-navigation :currentDate="$currentDateNav" :previousDayUrl="$previousDayUrl" :nextDayUrl="$nextDayUrl" />

        <x-shared.attendance-list-table :attendances="$attendances" firstColumn="name" routeName="admin.attendance.show" />


    </div>
@endsection
