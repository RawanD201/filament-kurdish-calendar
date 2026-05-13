<?php

namespace Entensy\FilamentKurdishCalendar\Forms\Components;

use Filament\Forms\Components\DatePicker;
use Entensy\FilamentKurdishCalendar\Forms\Components\Concerns\ConfiguresKurdishCalendarPicker;

class KurdishDatePicker extends DatePicker
{
    use ConfiguresKurdishCalendarPicker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpKurdishCalendarPicker();
    }
}
