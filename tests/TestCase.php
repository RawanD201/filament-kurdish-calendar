<?php

namespace Rawand201\FilamentKurdishCalendar\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rawand201\FilamentKurdishCalendar\FilamentKurdishCalendarServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentKurdishCalendarServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('app.timezone', 'UTC');
    }
}
