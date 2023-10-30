<?php

namespace Alikhedmati\Otp;

use Alikhedmati\Otp\Contracts\OtpInterface;
use Illuminate\Support\ServiceProvider;

class OtpServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'otp');
        $this->publishes([
            __DIR__ . '/../config/OTP.php' =>   config_path('OTP.php')
        ], 'config');
    }

    /**
     * @return void
     */

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/OTP.php', 'otp');
        $this->app->bind('OTP', fn() => new Otp());
    }
}