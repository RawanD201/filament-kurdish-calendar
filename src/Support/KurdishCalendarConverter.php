<?php

namespace Rawand201\FilamentKurdishCalendar\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/**
 * Kurdish calendar: Nawroz is month 1, day 1. Displayed Kurdish
 * year is gregorian_cycle_year + 700. Month lengths follow config
 * (default 6×31 + 5×30 + 29); the last month is adjusted so the Kurdish year
 * matches the real span from Nawroz to the next Nawroz.
 */
final class KurdishCalendarConverter
{
    public const YEAR_OFFSET = 700;

    public const NAWROZ_MONTH = 3;

    public const NAWROZ_DAY = 21;

    /** @var array<int, int> */
    public const MONTH_LENGTHS_BASE = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

    /**
     * @return array{year: int, month: int, day: int}
     */
    public static function fromGregorian(CarbonInterface $gregorian, ?string $timezone = null): array
    {
        $tz = $timezone ?? config('filament-kurdish-calendar.timezone') ?? config('app.timezone');
        $g = Carbon::parse($gregorian)->copy()->setTimezone($tz)->startOfDay();

        $nm = self::NAWROZ_MONTH;
        $nd = self::NAWROZ_DAY;

        $gy = (int) $g->year;
        $nawrozThisGy = self::nawrozInstant($gy, $nm, $nd, $tz);

        if ($g->lt($nawrozThisGy)) {
            $gregorianCycleYear = $gy - 1;
            $yearStart = self::nawrozInstant($gy - 1, $nm, $nd, $tz);
        } else {
            $gregorianCycleYear = $gy;
            $yearStart = $nawrozThisGy;
        }

        $kurdishYear = $gregorianCycleYear + self::YEAR_OFFSET;

        $yearEnd = $yearStart->copy()->addYear();
        $totalDays = (int) $yearStart->diffInDays($yearEnd);
        $lengths = self::monthLengthsForSpan($totalDays);

        $dayIndex = (int) $yearStart->diffInDays($g);
        if ($dayIndex < 0 || $dayIndex >= $totalDays) {
            throw new InvalidArgumentException('Gregorian date outside resolved Kurdish year span.');
        }

        $remaining = $dayIndex;
        for ($month = 1; $month <= 12; $month++) {
            $len = $lengths[$month - 1];
            if ($remaining < $len) {
                return [
                    'year' => $kurdishYear,
                    'month' => $month,
                    'day' => $remaining + 1,
                ];
            }
            $remaining -= $len;
        }

        throw new InvalidArgumentException('Could not map day index to Kurdish month.');
    }

    public static function toGregorian(int $kurdishYear, int $kurdishMonth, int $kurdishDay, ?string $timezone = null): Carbon
    {
        $tz = $timezone ?? config('filament-kurdish-calendar.timezone') ?? config('app.timezone');
        $nm = self::NAWROZ_MONTH;
        $nd = self::NAWROZ_DAY;

        if ($kurdishMonth < 1 || $kurdishMonth > 12) {
            throw new InvalidArgumentException('Kurdish month must be 1–12.');
        }

        $gregorianCycleYear = $kurdishYear - self::YEAR_OFFSET;

        $yearStart = self::nawrozInstant($gregorianCycleYear, $nm, $nd, $tz);
        $yearEnd = $yearStart->copy()->addYear();
        $totalDays = (int) $yearStart->diffInDays($yearEnd);
        $lengths = self::monthLengthsForSpan($totalDays);

        if ($kurdishDay < 1 || $kurdishDay > $lengths[$kurdishMonth - 1]) {
            throw new InvalidArgumentException('Kurdish day out of range for that month and year.');
        }

        $offset = 0;
        for ($m = 1; $m < $kurdishMonth; $m++) {
            $offset += $lengths[$m - 1];
        }
        $offset += $kurdishDay - 1;

        return $yearStart->copy()->addDays($offset)->startOfDay();
    }

    private static function nawrozInstant(int $gregorianYear, int $month, int $day, string $timezone): Carbon
    {
        return Carbon::create($gregorianYear, $month, $day, 0, 0, 0, $timezone)->startOfDay();
    }

    /**
     * @return array<int, int> 12 integers, one per month
     */
    private static function monthLengthsForSpan(int $totalDays): array
    {
        $base = self::MONTH_LENGTHS_BASE;

        $sum = array_sum($base);
        $diff = $totalDays - $sum;
        $lengths = $base;
        $lengths[11] += $diff;

        if ($lengths[11] < 1) {
            throw new InvalidArgumentException('Kurdish calendar month_lengths base sum is too large for this Nawroz span.');
        }

        return $lengths;
    }
}
