@props(['currentDate', 'previousDayUrl' => '#', 'nextDayUrl' => '#'])

<div class="nav-calendar">
    {{-- 前日リンク --}}
    <a href="{{ $previousDayUrl }}" class="nav-calendar__link">
        <img class="nav-left-arrow__icon" src="{{ asset('img/left-arrow.png') }}" alt="">前日
    </a>

    {{-- 現在日付 --}}
    <div class="nav-calendar__current">
        <img class="nav-calendar__icon" src="{{ asset('img/calendar.png') }}" alt="">
        <span class="nav-calendar__text">{{ $currentDate }}</span>
    </div>

    {{-- 翌日リンク --}}
    <a href="{{ $nextDayUrl }}" class="nav-calendar__link">
        翌日<img class="nav-right-arrow__icon" src="{{ asset('img/right-arrow.png') }}" alt="">
    </a>
</div>
