<?php

return [
    'length'    =>  env('OTP_LENGTH', 5),
    'expires_after' =>  env('OTP_EXPIRES_AFTER', 30),
    'characters'    =>  [1,2,3,4,5,6,7,8,9]
];