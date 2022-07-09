<?php

namespace Alikhedmati\Otp;

use Alikhedmati\Otp\Contracts\OtpInterface;
use Illuminate\Support\Carbon;

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

    public function set()
    {

    }

    public function verify()
    {

    }

    public function generate()
    {

    }
}