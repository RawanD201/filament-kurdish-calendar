<?php

namespace Rawand\FilamentKurdishCalendar\Forms\Components;

use Filament\Forms\Components\DatePicker;
use Rawand\FilamentKurdishCalendar\Forms\Components\Concerns\ConfiguresKurdishCalendarPicker;

class KurdishDatePicker extends DatePicker
{
    use ConfiguresKurdishCalendarPicker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpKurdishCalendarPicker();
    }
}
