<?php

namespace App\Providers;

use App\Support\Audit;
use App\Models\Setting;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $settings = Cache::remember('app.settings', 300, function () {
            return Setting::query()->pluck('value', 'key')->toArray();
        });

        if (! empty($settings['platform_name'])) {
            config(['app.name' => $settings['platform_name']]);
        }

        View::share('branding', [
            'platform_name' => $settings['platform_name'] ?? config('app.name', 'Estate Admin'),
            'platform_tagline' => $settings['platform_tagline'] ?? null,
            'primary_color' => $settings['primary_color'] ?? null,
            'logo_url' => $settings['logo_url'] ?? null,
            'favicon_url' => $settings['favicon_url'] ?? null,
        ]);

        Event::listen(Login::class, function (Login $event): void {
            Audit::log('login', 'User logged in', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
        });
    }
}
