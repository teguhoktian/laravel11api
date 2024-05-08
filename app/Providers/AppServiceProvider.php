<?php

namespace App\Providers;

use App\Settings\GeneralSetting;
use Illuminate\Auth\Notifications\ResetPassword;
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
        try {
            $settings = app(GeneralSetting::class);
            config(['app.timezone' => $settings->timezone]);
            config(['app.locale' => $settings->locale]);
            config(['app.url' => $settings->site_url]);
            config(['app.asset_url' => $settings->site_url]);
            config(['app.name' => $settings->site_name]);
        } catch (\Throwable $th) {
            // No Action
        }

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
