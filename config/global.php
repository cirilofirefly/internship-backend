<?php

return [
    'password_regex'    => '^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}$',
    'default_password'  => env('APP_DEFAULT_PASSWORD')
];
