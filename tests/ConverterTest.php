<?php

use Illuminate\Support\Carbon;
use Rawand201\FilamentKurdishCalendar\Support\KurdishCalendarConverter;

it('maps Nawroz to month 1 day 1', function () {
    $k = KurdishCalendarConverter::fromGregorian(Carbon::create(2026, 3, 21, 12, 0, 0, 'UTC'));

    expect($k['month'])->toBe(1)
        ->and($k['day'])->toBe(1);
});

it('keeps month 1 within March 21–April 20 span', function () {
    $k1 = KurdishCalendarConverter::fromGregorian(Carbon::create(2026, 3, 21, 0, 0, 0, 'UTC'));
    $k2 = KurdishCalendarConverter::fromGregorian(Carbon::create(2026, 4, 20, 23, 59, 59, 'UTC'));
    $k3 = KurdishCalendarConverter::fromGregorian(Carbon::create(2026, 4, 21, 0, 0, 0, 'UTC'));

    expect($k1['month'])->toBe(1)
        ->and($k2['month'])->toBe(1)
        ->and($k3['month'])->toBe(2);
});

it('round-trips a Kurdish date back to Gregorian', function () {
    $g = KurdishCalendarConverter::toGregorian(2726, 1, 1, 'UTC');

    expect($g->toDateString())->toBe('2026-03-21');
});
