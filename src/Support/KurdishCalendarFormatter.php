<?php

namespace Rawand201\FilamentKurdishCalendar\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class KurdishCalendarFormatter
{
    /**
     * Format a single instant: Kurdish date tokens (Y y m n d j F) plus Gregorian clock
     * tokens (H h i s g G A a) from the same moment in $timezone.
     */
    public static function format(CarbonInterface $instant, string $format, ?string $timezone = null): string
    {
        $tz = $timezone ?? config('filament-kurdish-calendar.timezone') ?? config('app.timezone');
        $g = Carbon::parse($instant)->copy()->setTimezone($tz);
        $k = KurdishCalendarConverter::fromGregorian($g, $tz);

        $locale = config('filament-kurdish-calendar.translation_locale');
        $monthKey = 'filament-kurdish-calendar::months.'.$k['month'];
        $F = filled($locale)
            ? __($monthKey, [], $locale)
            : __($monthKey);

        $Y = str_pad((string) $k['year'], 4, '0', STR_PAD_LEFT);
        $y = substr($Y, -2);
        $m = str_pad((string) $k['month'], 2, '0', STR_PAD_LEFT);
        $n = (string) $k['month'];
        $d = str_pad((string) $k['day'], 2, '0', STR_PAD_LEFT);
        $j = (string) $k['day'];

        $len = strlen($format);
        $out = '';
        for ($i = 0; $i < $len; $i++) {
            if ($format[$i] === '\\' && $i + 1 < $len) {
                $out .= $format[++$i];

                continue;
            }

            $out .= match ($format[$i]) {
                'Y' => $Y,
                'y' => $y,
                'm' => $m,
                'n' => $n,
                'd' => $d,
                'j' => $j,
                'F' => $F,
                'H' => $g->format('H'),
                'h' => $g->format('h'),
                'i' => $g->format('i'),
                's' => $g->format('s'),
                'g' => $g->format('g'),
                'G' => $g->format('G'),
                'A' => $g->format('A'),
                'a' => $g->format('a'),
                default => $format[$i],
            };
        }

        return $out;
    }
}
