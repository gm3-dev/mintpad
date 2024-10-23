<?php

return [
    1 => [
        'url' => env('ES_URL', false),
        'key' => env('ES_KEY', false),
        'coingecko' => 'ethereum'
    ],
    10 => [
        'url' => env('OS_URL', false),
        'key' => env('OS_KEY', false),
        'coingecko' => 'optimism'
    ],
    56 => [
        'url' => env('BS_URL', false),
        'key' => env('BS_KEY', false),
        'coingecko' => 'binancecoin'
    ],
    137 => [
        'url' => env('PS_URL', false),
        'key' => env('PS_KEY', false),
        'coingecko' => 'matic-network'
    ],
    250 => [
        'url' => env('FS_URL', false),
        'key' => env('FS_KEY', false),
        'coingecko' => 'fantom'
    ],
    42161 => [
        'url' => env('AS_URL', false),
        'key' => env('AS_KEY', false),
        'coingecko' => 'arbitrum'
    ],
    5000 => [
        'url' => env('ME_URL', false),
        'key' => env('ME_KEY', false),
        'coingecko' => 'mantle'
    ],
    2000 => [
        'url' => env('DE_URL', false),
        'key' => env('DE_KEY', false),
        'coingecko' => 'dogecoin'
    ]
];
