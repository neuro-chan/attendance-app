@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/components/shared/attendance-detail-table.css') }}">
@endsection

@section('content')
    <div class="attendance-detail">
        <h1 class="attendance-detail__title">勤怠詳細</h1>

        <form class="attendance-detail__form" method="POST" action="{{ route('admin.correction.approve.store', $correction->id) }}">
            @csrf

            <x-shared.attendance-detail-table
                :attendance="$correction->attendance"
                :userName="$correction->user->name"
                :disabled="true"
            />

            <div class="attendance-detail__actions">
                @if ($correction->isPending())
                    <button class="attendance-detail__button" type="submit">承認</button>
                @else
                    <button class="attendance-detail__button--approved" type="button" disabled>承認済み</button>
                @endif
            </div>

        </form>
    </div>
@endsection
