<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Device status update every minute
        $schedule->command('devices:update-status')->everyMinute();

        // Force update all devices every 5 minutes
        $schedule->command('devices:update-status --force')->everyFiveMinutes();

        // Clean old device logs daily at 2 AM
        $schedule->command('devices:clean-logs')->dailyAt('02:00');

        // Send battery alerts every 10 minutes
        $schedule->command('devices:check-battery')->everyTenMinutes();
        })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
