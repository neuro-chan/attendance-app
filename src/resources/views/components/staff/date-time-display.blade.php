@props(['now'])

<div class="attendance-date-time">
    <p class="attendance-date-time__date">
        {{ $now->isoFormat('YYYY年M月D日(dd)') }}
    </p>

    <p class="attendance-date-time__time">
        {{ $now->format('H:i') }}
    </p>
</div>
