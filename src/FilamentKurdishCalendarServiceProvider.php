<?php

namespace Rawand201\FilamentKurdishCalendar;

use Closure;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;
use Rawand201\FilamentKurdishCalendar\Support\KurdishCalendarFormatter;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentKurdishCalendarServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-kurdish-calendar')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            AlpineComponent::make('kurdish-date-picker', __DIR__.'/../resources/js/kurdish-date-picker.js'),
        ], 'rawand201/filament-kurdish-calendar');

        TextColumn::macro('kurdishDate', function (string|Closure|null $format = null, ?string $timezone = null) {
            $format ??= fn (): string => (string) config(
                'filament-kurdish-calendar.default_date_format',
                'Y/m/d',
            );

            /** @var TextColumn $column */
            $column = $this;
            $column->formatStateUsing(static function (TextColumn $column, $state) use ($format, $timezone): ?string {
                if (blank($state)) {
                    return null;
                }

                /** @var string $resolvedFormat */
                $resolvedFormat = $column->evaluate($format);

                $tz = $timezone ?? $column->getTimezone();

                return KurdishCalendarFormatter::format(
                    Carbon::parse($state),
                    $resolvedFormat,
                    $tz,
                );
            });

            return $column;
        });

        TextColumn::macro('kurdishDateTime', function (string|Closure|null $format = null, ?string $timezone = null) {
            $format ??= fn (): string => (string) config(
                'filament-kurdish-calendar.default_datetime_format',
                'Y/m/d H:i',
            );

            /** @var TextColumn $column */
            $column = $this;
            $column->formatStateUsing(static function (TextColumn $column, $state) use ($format, $timezone): ?string {
                if (blank($state)) {
                    return null;
                }

                /** @var string $resolvedFormat */
                $resolvedFormat = $column->evaluate($format);

                $tz = $timezone ?? $column->getTimezone();

                return KurdishCalendarFormatter::format(
                    Carbon::parse($state),
                    $resolvedFormat,
                    $tz,
                );
            });

            return $column;
        });

        TextEntry::macro('kurdishDate', function (string|Closure|null $format = null, ?string $timezone = null) {
            $format ??= fn (): string => (string) config(
                'filament-kurdish-calendar.default_date_format',
                'Y/m/d',
            );

            /** @var TextEntry $entry */
            $entry = $this;
            $entry->formatStateUsing(static function (TextEntry $component, $state) use ($format, $timezone): ?string {
                if (blank($state)) {
                    return null;
                }

                /** @var string $resolvedFormat */
                $resolvedFormat = $component->evaluate($format);

                $tz = $component->evaluate($timezone) ?? $component->getTimezone();

                return KurdishCalendarFormatter::format(
                    Carbon::parse($state),
                    $resolvedFormat,
                    $tz,
                );
            });

            return $entry;
        });

        TextEntry::macro('kurdishDateTime', function (string|Closure|null $format = null, ?string $timezone = null) {
            $format ??= fn (): string => (string) config(
                'filament-kurdish-calendar.default_datetime_format',
                'Y/m/d H:i',
            );

            /** @var TextEntry $entry */
            $entry = $this;
            $entry->formatStateUsing(static function (TextEntry $component, $state) use ($format, $timezone): ?string {
                if (blank($state)) {
                    return null;
                }

                /** @var string $resolvedFormat */
                $resolvedFormat = $component->evaluate($format);

                $tz = $component->evaluate($timezone) ?? $component->getTimezone();

                return KurdishCalendarFormatter::format(
                    Carbon::parse($state),
                    $resolvedFormat,
                    $tz,
                );
            });

            return $entry;
        });

    }
}
