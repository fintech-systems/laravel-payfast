<?php

return [
    'merchant_id' => env('PAYFAST_MERCHANT_ID', '10004002'),
    'merchant_key' => env('PAYFAST_MERCHANT_KEY', 'q1cd2rdny4a53'),
    'passphrase' => env('PAYFAST_PASSPHRASE', 'payfast'),
    'testmode' => env('PAYFAST_TESTMODE', true),
    // You can overide the default callback URLs
    'return_url' => env('PAYFAST_RETURN_URL', config('app.url' . '/payfast/success')),
    'cancel_url' => env('PAYFAST_CANCEL_URL', config('app.url' . '/payfast/cancel')),
];
