@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/components/shared/request-list-table.css') }}">
@endsection

@section('content')
    <div class="request-list">
        <h1 class="request-list__title">申請一覧</h1>

        <x-shared.request-list-table
            :corrections="$corrections"
            :currentStatus="$status"
            :pendingUrl="route('request.index', ['status' => 'pending'])"
            :approvedUrl="route('request.index', ['status' => 'approved'])"
            :isAdmin="true"
        />
    </div>
@endsection
