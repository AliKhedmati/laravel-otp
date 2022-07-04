<?php

namespace Alikhedmati\OtpAuth\Facades;

use Illuminate\Support\Facades\Facade;

class OtpAuth extends Facade
{
    /**
     * @return string
     */

    protected static function getFacadeAccessor(): string
    {
        return OtpAuth::class;
    }
}