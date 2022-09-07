<?php

return [
    // Ethereum
    1 => [
        'name' => 'ethereum',
        'metamask' => 'homestead',
        'id' => 1,
        'token' => 'ETH',
        'full' => 'Ethereum',
        'testnet' => false,
        'rpc' => 'https://eth-mainnet.g.alchemy.com/v2/ZVtoxx-JliMx1W7Ui1NoDn2Fmjs59DCa'
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
        'testnet' => true,
        'rpc' => 'https://eth-goerli.g.alchemy.com/v2/yx3q1Jhb-P5seWxrD0tzcuIFZg01A3_F'
    ],
    // Polygon
    137 => [
        'name' => 'polygon',
        'metamask' => 'matic',
        'id' => 137,
        'token' => 'MATIC',
        'full' => 'Polygon',
        'testnet' => false,
        'rpc' => 'https://polygon-mainnet.g.alchemy.com/v2/8r-nRwRsVh-xJuiaT3U179oIHbIKm0mW'
    ],
    80001 => [
        'name' => 'mumbai',
        'metamask' => 'maticmum',
        'id' => 80001,
        'token' => 'MATIC',
        'full' => 'Mumbai',
        'testnet' => true,
        'rpc' => 'https://polygon-mumbai.g.alchemy.com/v2/4-no0a1q4yrq3-vtoByaqB2qbKdpGrZf'
    ],
    // Fantom
    250 => [
        'name' => 'fantom',
        'metamask' => 'unknown',
        'id' => 250,
        'token' => 'FTM',
        'full' => 'Fantom',
        'testnet' => false,
        'rpc' => false
    ],
    4002 => [
        'name' => 'fantom-testnet',
        'metamask' => 'unknown',
        'id' => 4002,
        'token' => 'FTM',
        'full' => 'Fantom testnet',
        'testnet' => true,
        'rpc' => false
    ],
    // Avalanche
    43114 => [
        'name' => 'avalanche',
        'metamask' => 'unknown',
        'id' => 43114,
        'token' => 'AVAX',
        'full' => 'Avalanche',
        'testnet' => false,
        'rpc' => false
    ],
    43113 => [
        'name' => 'avalanche-testnet',
        'metamask' => 'unknown',
        'id' => 43113,
        'token' => 'AVAX',
        'full' => 'Avalanche testnet',
        'testnet' => true,
        'rpc' => false
    ],
    // Optimism
    // 10 => [
    //     'name' => 'optimism',
    //     'metamask' => 'unknown',
    //     'id' => 10,
    //     'token' => 'ETH',
    //     'full' => 'Optimism',
    //     'testnet' => false,
    //     'rpc' => false
    // ],
    // 69 => [
    //     'name' => 'optimism-testnet',
    //     'metamask' => 'unknown',
    //     'id' => 69,
    //     'token' => 'ETH',
    //     'full' => 'Optimism testnet',
    //     'testnet' => true,
    //     'rpc' => false
    // ],
    // Arbitrum
    // 42161 => [
    //     'name' => 'arbitrum',
    //     'metamask' => 'unknown',
    //     'id' => 42161,
    //     'token' => 'ETH',
    //     'full' => 'Arbitrum',
    //     'testnet' => false,
    //     'rpc' => false
    // ],
    // 421611 => [
    //     'name' => 'arbitrum-testnet',
    //     'metamask' => 'unknown',
    //     'id' => 421611,
    //     'token' => 'ETH',
    //     'full' => 'Arbitrum testnet',
    //     'testnet' => true,
    //     'rpc' => false
    // ],
    // BNB
    56 => [
        'name' => 'binance',
        'metamask' => 'bnb',
        'id' => 56,
        'token' => 'BNB',
        'full' => 'Binance Smart Chain',
        'testnet' => false,
        'rpc' => false
    ],
    97 => [
        'name' => 'binance-testnet',
        'metamask' => 'bnbt',
        'id' => 97,
        'token' => 'tBNB',
        'full' => 'Binance Smart Chain testnet',
        'testnet' => true,
        'rpc' => false
    ],
];
