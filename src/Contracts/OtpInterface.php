<?php

namespace Alikhedmati\Otp\Contracts;

interface OtpInterface
{
    public function setKey(string $key);

    public function getKey(): string;

    public function setLength(int $length);

    public function getLength(): int;
    
    public function setExpiresAfter(int $seconds);

    public function getExpiresAt();

    public function createAndRemember(): int;

    public function verify(int $token): void;
    
    public function generate(): int;
}