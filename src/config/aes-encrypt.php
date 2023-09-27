<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Configure the Aes encryption
    |--------------------------------------------------------------------------
    |
    | - key (string) - They key used to encrypt and decrypt your data.
    |
    */

    'key' => env('AES_ENCRYPT_KEY', 'Your Encrypted Key'),

    /*
    |--------------------------------------------------------------------------
    | Configure the Aes encryption
    |--------------------------------------------------------------------------
    |
    | - mode (string) - Encryption method.
    |
    | Supported: "aes-{key length: 128, 192, 256}-{mode: ECB, CBC, CFB1, CFB8, CFB128, OFB}"
    |
    | For example: aes-256-cbc or aes-128-ecb.
    |
    */

    'mode' => env('AES_ENCRYPT_MODE', 'aes-256-cbc'),
];
