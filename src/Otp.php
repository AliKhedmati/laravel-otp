<?php

namespace Alikhedmati\Otp;

use Alikhedmati\Otp\Contracts\OtpInterface;
use Alikhedmati\Otp\Exceptions\OtpException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class Otp implements OtpInterface
{
    protected string $key;

    protected int $length;

    protected Carbon $expiresAt;

    protected string|int $otp;

    public function __construct()
    {
        $this->length = config('otp.length');

        $this->expiresAt = now()->addSeconds(config('otp.expires_after'));
    }

    /**
     * @param string $key
     * @return $this
     */

    public function key(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param int $length
     * @return $this
     */

    public function length(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @param int $seconds
     * @return $this
     */

    public function expiresAfter(int $seconds): static
    {
        $this->expiresAt = now()->addSeconds($seconds);

        return $this;
    }

    /**
     * @return int
     * @throws OtpException
     */

    public function set(): int
    {
        /**
         * Check Redis and handle Rate-Limiter.
         */

        $otp = Redis::get('otp-' . $this->key);

        if ($otp){

            $otp = json_decode($otp);

            if (now()->lessThan($otp->expires_at)){

                throw new OtpException(trans('otp.wait', [
                    'seconds'   =>  now()->diffInSeconds($otp->expires_at)
                ]));

            }

        }

        /**
         * Generate an OTP.
         */

        $otp = self::generate();

        /**
         * Store in Redis.
         */

        Redis::set('otp-'. $this->key, json_encode([
            'expires_at'   =>  $this->expiresAt,
            'otp'   =>  $otp
        ]));

        return $otp;
    }

    /**
     * @param int $token
     * @return void
     * @throws OtpException
     */

    public function verify(int $token): void
    {
        /**
         * Seek Redis.
         */

        $otp = Redis::get('otp-'. $this->key);

        if (!$otp){

            throw new OtpException(trans('otp.failed'));

        }

        $otp = json_decode($otp);

        /**
         * Validate expiration time.
         */

        if (now()->greaterThan($otp->expires_at)){

            throw new OtpException(trans('otp.expired'));

        }

        /**
         * Validate token.
         */

        if ($otp->otp != $token){

            throw new OtpException(trans('otp.not-valid'));

        }
    }

    /**
     * @return int
     */

    public function generate(): int
    {
        return mt_rand(pow(10, $this->length - 1), pow(10, $this->length) - 1);
    }
}