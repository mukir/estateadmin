<?php

use App\Http\Middleware\EnsureBusinessAccess;
use App\Http\Middleware\EnsureMfa;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SetBusinessContext;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'business' => SetBusinessContext::class,
            'business.access' => EnsureBusinessAccess::class,
            'role' => EnsureRole::class,
            'mfa' => EnsureMfa::class,
        ]);
    })
    ->withSchedule(function ($schedule): void {
        $schedule->command('billing:run')->monthlyOn(1, '02:00');
        $schedule->command('billing:reminders')->dailyAt('08:00');
        $schedule->command('reminders:invoices')->dailyAt('07:30');
        $schedule->command('reports:send-daily')->dailyAt('06:30');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e): void {
            Log::error('Unhandled exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        });
    })->create();
