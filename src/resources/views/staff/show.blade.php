@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/components/shared/attendance-detail-table.css') }}">
@endsection

@section('content')
    <div class="attendance-detail">
        <h1 class="attendance-detail__title">勤怠詳細</h1>

        <form class="attendance-detail__form" method="POST" action="{{ route('correction.store', $attendance->id) }}">
            @csrf

            <x-shared.attendance-detail-table :attendance="$attendance" :disabled="$isPending" />

            <div class="attendance-detail__actions">
                @if (!$isPending)
                    <button class="attendance-detail__button" type="submit">修正</button>
                @endif
                @if ($isPending)
                    <p class="error-text">*承認待ちのため修正はできません。</p>
                @endif
            </div>

        </form>
    </div>
@endsection
