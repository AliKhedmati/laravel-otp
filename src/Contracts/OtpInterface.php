<?php

namespace Alikhedmati\Otp\Contracts;

interface OtpInterface
{
    public function key(string $key);
    
    public function length(int $length);

    public function expiresAfter(int $seconds);
    
    public function set();

    public function verify(int $token);
    
    public function generate();
}