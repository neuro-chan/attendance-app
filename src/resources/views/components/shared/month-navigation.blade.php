@props(['currentMonth', 'previousMonthUrl' => '#', 'nextMonthUrl' => '#'])

<div class="month-navigation">
    <a href="{{ $previousMonthUrl }}" class="month-navigation__link">
        <i class="fa-solid fa-arrow-left"></i> 前月
    </a>

    <div class="month-navigation__current">
        <img class="month-navigation__icon" src="{{ asset('img/calendar.png') }}" alt="">
        <span class="month-navigation__text">{{ $currentMonth }}</span>
    </div>

    <a href="{{ $nextMonthUrl }}" class="month-navigation__link">
        翌月 <i class="fa-solid fa-arrow-right"></i>
    </a>
</div>
