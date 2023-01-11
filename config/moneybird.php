<?php

return [
    'token' => env('MB_TOKEN'),
    'vat' => [
        'nl' => env('MB_VAT_NL'),
        'eu' => env('MB_VAT_EU'),
        'other' => env('MB_VAT_OTHER')
    ]
];
