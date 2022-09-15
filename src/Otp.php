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

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param int $length
     * @return $this
     */

    public function setLength(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return int
     */

    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $seconds
     * @return $this
     */

    public function setExpiresAfter(int $seconds): static
    {
        $this->expiresAt = now()->addSeconds($seconds);

        return $this;
    }

    public function getExpiresAt(): Carbon
    {
        return $this->expiresAt;
    }

    /**
     * @return int
     * @throws OtpException
     */

    public function createAndRemember(): int
    {
        /**
         * Check Redis and handle Rate-Limiter.
         */

        $otp = Redis::get('otp-' . $this->key);

        if ($otp){

            $otp = json_decode($otp);

            if (now()->lessThan($otp->expires_at)){

                throw new OtpException(trans('otp::messages.wait', [
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

        Redis::set('otp:'. $this->key, json_encode([
            'expires_at'   =>  $this->expiresAt,
            'otp'   =>  $otp
        ]), $this->expiresAt);

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

        $otp = Redis::get('otp:'. $this->key);

        if (!$otp){

            throw new OtpException(trans('otp::messages.failed'));

        }

        $otp = json_decode($otp);

        /**
         * Validate expiration time.
         */

        if (now()->greaterThan($otp->expires_at)){

            throw new OtpException(trans('otp::messages.expired'));

        }

        /**
         * Validate token.
         */

        if ($otp->otp != $token){

            throw new OtpException(trans('otp::messages.not-valid'));

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