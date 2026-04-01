# Filament Kurdish Calendar

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rawand201/filament-kurdish-calendar.svg?style=flat-square)](https://packagist.org/packages/rawand201/filament-kurdish-calendar)
[![Total Downloads](https://img.shields.io/packagist/dt/rawand201/filament-kurdish-calendar.svg?style=flat-square)](https://packagist.org/packages/rawand201/filament-kurdish-calendar)
[![License](https://img.shields.io/packagist/l/rawand201/filament-kurdish-calendar.svg?style=flat-square)](https://packagist.org/packages/rawand201/filament-kurdish-calendar)
[![CI](https://github.com/RawanD201/filament-kurdish-calendar/actions/workflows/ci.yml/badge.svg)](https://github.com/RawanD201/filament-kurdish-calendar/actions/workflows/ci.yml)

Kurdish calendar formatting and picker UI for Filament.

This package **does not change database storage**. Values are still stored as **Gregorian** dates/times; the package only changes how they are **displayed** in Filament and how the picker UI is presented.

## Requirements

|          | Version                                                 |
| -------- | ------------------------------------------------------- |
| PHP      | **8.2**, **8.3**, **8.4**, **8.5** (`>=8.2 <8.6` in Composer) |
| Laravel  | 11.x / 12.x                                             |
| Filament | ^4.0 \| ^5.0                                            |

## Installation

```bash
composer require rawand201/filament-kurdish-calendar
```

```bash
php artisan vendor:publish --tag=filament-kurdish-calendar-config
```

### Publish translations (optional)

```bash
php artisan vendor:publish --tag=filament-kurdish-calendar-translations
```

### Publish views (optional)

```bash
php artisan vendor:publish --tag=filament-kurdish-calendar-views
```

## Usage

**Tables**

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('created_at')->kurdishDate();
TextColumn::make('updated_at')->kurdishDateTime();
```

**Infolists**

```php
use Filament\Infolists\Components\TextEntry;

TextEntry::make('created_at')->kurdishDate();
TextEntry::make('updated_at')->kurdishDateTime();
```

**Forms (Kurdish picker UI)**

```php
use Rawand201\FilamentKurdishCalendar\Forms\Components\KurdishDatePicker;

KurdishDatePicker::make('birth_date');
```

## Calendar rules

- **Month names** come from translations `filament-kurdish-calendar::months` (publish/override as needed).

## Formatting

### Supported tokens

Kurdish calendar: `Y` `y` `m` `n` `d` `j` `F` (full month name).  
Clock (same instant, app/picker timezone): `H` `h` `i` `s` `g` `G` `A` `a`.  
Backslash escapes the next character.

Example:

```php
TextColumn::make('created_at')->kurdishDate('j F Y');
```

## Programmatic conversion

```php
use Rawand201\FilamentKurdishCalendar\Support\KurdishCalendarConverter;
use Rawand201\FilamentKurdishCalendar\Support\KurdishCalendarFormatter;
use Illuminate\Support\Carbon;

$parts = KurdishCalendarConverter::fromGregorian(Carbon::now());
$gregorian = KurdishCalendarConverter::toGregorian($parts['year'], $parts['month'], $parts['day']);
$label = KurdishCalendarFormatter::format(Carbon::now(), 'Y/m/d H:i');
```

## Configuration

Published file: `config/filament-kurdish-calendar.php`

```php
return [
    // null = use app.timezone
    'timezone' => null,

    // Default display formats (Kurdish tokens + optional clock tokens)
    'default_date_format' => 'Y/m/d',
    'default_datetime_format' => 'Y/m/d H:i',

    // null = use app locale
    'translation_locale' => null,

    // Flatpickr locale used by the picker UI
    'picker_locale' => 'ckb_IQ',
];
```

## Development

```bash
composer format
composer test
composer analyse
```

## Compatibility

- **PHP**: `>=8.2 <8.6` (8.2–8.5) — tested on **8.2**, **8.3**, **8.4**, and **8.5**
- **Laravel**: 11.x / 12.x
- **Filament**: ^4.0 \| ^5.0

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Security

See [SECURITY.md](SECURITY.md).

## Local development (path repository)

If the package is a Composer **path** repo, code updates are immediate, but Laravel may still cache config/views:

```bash
php artisan optimize:clear
```

If you published `config/filament-kurdish-calendar.php` into the app, merge new keys from the package or delete the published file and rely on the package defaults.

## License

MIT. See [LICENSE.md](LICENSE.md).
