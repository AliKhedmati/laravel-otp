<?php

namespace Alikhedmati\Otp;

use Alikhedmati\Otp\Contracts\OtpInterface;
use Illuminate\Support\ServiceProvider;

class OTPServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();
    }

    protected function offerPublishing()
    {
        $this->publishes([
            __DIR__ .'config/otp.php'   =>   config_path('otp.php')
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . 'config/otp.php', 'otp');

        $this->app->bind(OtpInterface::class, fn() => new Otp());
    }
}