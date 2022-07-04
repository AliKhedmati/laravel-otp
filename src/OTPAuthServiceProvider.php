<?php

namespace Alikhedmati\OtpAuth;

use Alikhedmati\OtpAuth\Contracts\OtpAuthInterface;
use Illuminate\Support\ServiceProvider;

class OTPAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . 'routes/api.php');
    }

    public function register()
    {
        $this->app->bind(OtpAuthInterface::class, fn() => new OtpAuth());
    }
}