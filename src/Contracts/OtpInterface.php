<?php

namespace Alikhedmati\Otp\Contracts;

interface OtpInterface
{
    public function setKey(string $key);
    public function setLength(int $length);
    public function setExpiresAfter(int $seconds);
    public function createAndRemember(): int;
    public function verify(int $token): void;
    public function generate(): int;
}