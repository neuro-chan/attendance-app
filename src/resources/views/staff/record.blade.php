@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/components/staff/record.css') }}">
@endsection

@section('content')
    <div class="attendance-record">
        <x-staff.status-badge :status="$status" />
        <x-staff.date-time-display :now="$now" />
        <x-staff.action-buttons :status="$status" />
    </div>
@endsection
