<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Timezone for calendar day boundaries
    |--------------------------------------------------------------------------
    |
    | Null falls back to app.timezone. Used when converting instants to Kurdish
    | year/month/day (start-of-day in this zone).
    |
    */
    'timezone' => null,

    /*
    |--------------------------------------------------------------------------
    | Default display formats (Kurdish calendar tokens)
    |--------------------------------------------------------------------------
    |
    | Supported: Y y m n d j F (Kurdish calendar) and H h i s g G A a (clock in
    | the configured timezone). Other characters are output literally.
    |
    */
    'default_date_format' => 'Y/m/d',

    'default_datetime_format' => 'Y/m/d H:i',

    /*
    |--------------------------------------------------------------------------
    | Translations for month names (filament-kurdish-calendar::months.N)
    |--------------------------------------------------------------------------
    |
    | Null uses the application locale.
    |
    */
    'translation_locale' => null,

    /*
    |--------------------------------------------------------------------------
    | Date picker UI (Flatpickr)
    |--------------------------------------------------------------------------
    |
    | Pickers still select a Gregorian date; values in the database stay
    | Gregorian. Only table/infolist formatters use Kurdish calendar rules.
    |
    */
    'picker_locale' => 'ckb_IQ',

];
