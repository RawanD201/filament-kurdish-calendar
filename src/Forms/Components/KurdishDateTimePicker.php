<?php

namespace Rawand201\FilamentKurdishCalendar\Forms\Components;

use Filament\Forms\Components\DateTimePicker;
use Rawand201\FilamentKurdishCalendar\Forms\Components\Concerns\ConfiguresKurdishCalendarPicker;

class KurdishDateTimePicker extends DateTimePicker
{
    use ConfiguresKurdishCalendarPicker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpKurdishCalendarPicker();
    }
}
