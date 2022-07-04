<?php

/**
 * Authentication.
 */

use Illuminate\Support\Facades\Route;

Route::prefix('authentication')->name('authentication.')->group(function (){

    /**
     * Access token.
     */

    Route::prefix('access-token')->name('access-token.')->group(function (){

        /**
         * Via OTP.
         */

        Route::prefix('via-otp')->name('via-otp.')->group(function (){

            /**
             * Send OTP.
             */

            Route::post('send', [ViaOTPController::class, 'send'])->name('send');

            /**
             * Verify OTP.
             */

            Route::post('verify', [ViaOTPController::class, 'verify'])->name('verify');

        });

    });

});
