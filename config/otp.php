<?php

return [
    'length'    =>  env('OTP_LENGTH', 5),
    'expires_after' =>  env('OTP_EXPIRES_AFTER', 60),
];