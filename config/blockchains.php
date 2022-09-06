<?php

return [
    // Ethereum
    1 => [
        'name' => 'ethereum',
        'metamask' => 'homestead',
        'id' => 1,
        'token' => 'ETH',
        'full' => 'Ethereum',
        'testnet' => false
    ],
    // 4 => [ // deprecated
    //     'name' => 'rinkeby',
    //     'metamask' => 'rinkeby',
    //     'id' => 4,
    //     'token' => 'ETH',
    //     'full' => 'Rinkeby',
    //     'testnet' => true
    // ],
    5 => [
        'name' => 'goerli',
        'metamask' => 'goerli',
        'id' => 5,
        'token' => 'ETH',
        'full' => 'Goerli',
        'testnet' => true
    ],
    // Polygon
    137 => [
        'name' => 'polygon',
        'metamask' => 'matic',
        'id' => 137,
        'token' => 'MATIC',
        'full' => 'Polygon',
        'testnet' => false
    ],
    80001 => [
        'name' => 'mumbai',
        'metamask' => 'maticmum',
        'id' => 80001,
        'token' => 'MATIC',
        'full' => 'Mumbai',
        'testnet' => true
    ],
    // Fantom
    250 => [
        'name' => 'fantom',
        'metamask' => 'unknown',
        'id' => 250,
        'token' => 'FTM',
        'full' => 'Fantom',
        'testnet' => false
    ],
    4002 => [
        'name' => 'fantom-testnet',
        'metamask' => 'unknown',
        'id' => 4002,
        'token' => 'FTM',
        'full' => 'Fantom testnet',
        'testnet' => true
    ],
    // Avalanche
    43114 => [
        'name' => 'avalanche',
        'metamask' => 'unknown',
        'id' => 43114,
        'token' => 'AVAX',
        'full' => 'Avalanche',
        'testnet' => false
    ],
    43113 => [
        'name' => 'avalanche-testnet',
        'metamask' => 'unknown',
        'id' => 43113,
        'token' => 'AVAX',
        'full' => 'Avalanche testnet',
        'testnet' => true
    ],
    // Optimism
    // 10 => [
    //     'name' => 'optimism',
    //     'metamask' => 'unknown',
    //     'id' => 10,
    //     'token' => 'ETH',
    //     'full' => 'Optimism',
    //     'testnet' => false
    // ],
    // 69 => [
    //     'name' => 'optimism-testnet',
    //     'metamask' => 'unknown',
    //     'id' => 69,
    //     'token' => 'ETH',
    //     'full' => 'Optimism testnet',
    //     'testnet' => true
    // ],
    // Arbitrum
    // 42161 => [
    //     'name' => 'arbitrum',
    //     'metamask' => 'unknown',
    //     'id' => 42161,
    //     'token' => 'ETH',
    //     'full' => 'Arbitrum',
    //     'testnet' => false
    // ],
    // 421611 => [
    //     'name' => 'arbitrum-testnet',
    //     'metamask' => 'unknown',
    //     'id' => 421611,
    //     'token' => 'ETH',
    //     'full' => 'Arbitrum testnet',
    //     'testnet' => true
    // ],
    // BNB
    56 => [
        'name' => 'binance',
        'metamask' => 'bnb',
        'id' => 56,
        'token' => 'BNB',
        'full' => 'Binance Smart Chain',
        'testnet' => false
    ],
    97 => [
        'name' => 'binance-testnet',
        'metamask' => 'bnbt',
        'id' => 97,
        'token' => 'tBNB',
        'full' => 'Binance Smart Chain testnet',
        'testnet' => true
    ],
];
