<?php

namespace Rawand\FilamentKurdishCalendar\Forms\Components\Concerns;

use Filament\Forms\Components\DateTimePicker;

trait ConfiguresKurdishCalendarPicker
{
    protected function setUpKurdishCalendarPicker(): void
    {
        /** @var view-string $view */
        $view = 'filament-kurdish-calendar::components.kurdish-date-time-picker';

        $this->native(false)
            ->displayFormat(function (DateTimePicker $component): string {
                if ($component->hasTime()) {
                    return (string) config(
                        'filament-kurdish-calendar.default_datetime_format',
                        'Y/m/d H:i',
                    );
                }

                return (string) config(
                    'filament-kurdish-calendar.default_date_format',
                    'Y/m/d',
                );
            })
            ->view($view);
    }
}
