@props(['currentMonth', 'previousMonthUrl' => '#', 'nextMonthUrl' => '#'])

<div class="nav-calendar">
    {{-- 前月リンク --}}
    <a href="{{ $previousMonthUrl }}" class="nav-calendar__link">
        <img class="nav-left-arrow__icon" src="{{ asset('img/left-arrow.png') }}" alt="">前月
    </a>

    {{-- 現在月 --}}
    <div class="nav-calendar__current">
        <img class="nav-calendar__icon" src="{{ asset('img/calendar.png') }}" alt="">
        <span class="nav-calendar__text">{{ $currentMonth }}</span>
    </div>

    {{-- 翌月リンク --}}
    <a href="{{ $nextMonthUrl }}" class="nav-calendar__link">
        翌月<img class="nav-right-arrow__icon" src="{{ asset('img/right-arrow.png') }}" alt="">
    </a>
</div>
