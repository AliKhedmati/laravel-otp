<?php

namespace Alikhedmati\Otp;

use Alikhedmati\Otp\Contracts\OtpInterface;
use Alikhedmati\Otp\Exceptions\OtpException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

class Otp implements OtpInterface
{
    /**
     * @var string
     */

    protected string $key;

    /**
     * @var int
     */

    protected int $length;

    /**
     * @var Carbon
     */

    protected Carbon $expiresAt;

    /**
     * @var string|int
     */

    protected string|int $otp;

    public function __construct()
    {
        $this->length = Config::get('otp.length', 6);
        $this->expiresAt = Carbon::now()->addSeconds(Config::get('otp.expires_after'));
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
     * @param int $length
     * @return $this
     */

    public function setLength(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @param int $seconds
     * @return $this
     */

    public function setExpiresAfter(int $seconds): static
    {
        $this->expiresAt = Carbon::now()->addSeconds($seconds);

        return $this;
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

        $otp = Redis::get('otp:' . $this->key);

        if ($otp) {

            $otp = json_decode($otp);

            if (Carbon::now()->lessThan($otp->expires_at)) {

                throw new OtpException(trans('otp::messages.wait', [
                    'seconds' => Carbon::now()->diffInSeconds($otp->expires_at)
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

        Redis::set('otp:' . $this->key, json_encode([
            'expires_at' => $this->expiresAt,
            'otp' => $otp
        ]));

        return $otp;
    }

    /**
     * @return int
     */

    public function generate(): int
    {
        return mt_rand(pow(10, $this->length - 1), pow(10, $this->length) - 1);
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

        $otp = Redis::get('otp:' . $this->key);

        if (!$otp) {

            throw new OtpException(trans('otp::messages.failed'));

        }

        $otp = json_decode($otp);

        /**
         * Validate expiration time.
         */

        if (Carbon::now()->greaterThan($otp->expires_at)) {

            throw new OtpException(trans('otp::messages.expired'));

        }

        /**
         * Validate token.
         */

        if ($otp->otp != $token) {

            throw new OtpException(trans('otp::messages.not-valid'));

        }
    }
}