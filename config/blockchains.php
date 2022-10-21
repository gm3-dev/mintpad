<?php

return [
    // Ethereum
    1 => [
        'name' => 'ethereum',
        'id' => 1,
        'token' => 'ETH',
        'full' => 'Ethereum',
        'testnet' => false,
        'rpc' => 'https://eth-mainnet.g.alchemy.com/v2/ZVtoxx-JliMx1W7Ui1NoDn2Fmjs59DCa',
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    // 4 => [ // deprecated
    //     'name' => 'rinkeby',
    //     'id' => 4,
    //     'token' => 'ETH',
    //     'full' => 'Rinkeby',
    //     'testnet' => true,
    //     'wallet' => ['metamask'],
    //     'chain' => 'evm'
    // ],
    5 => [
        'name' => 'goerli',
        'id' => 5,
        'token' => 'ETH',
        'full' => 'Ethereum Goerli',
        'testnet' => true,
        'rpc' => 'https://eth-goerli.g.alchemy.com/v2/yx3q1Jhb-P5seWxrD0tzcuIFZg01A3_F',
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    // Solana (chain ID is made up)
    11001 => [
        'name' => 'mainnet-beta',
        'id' => 11001,
        'token' => 'SOL',
        'full' => 'Solana',
        'testnet' => false,
        'rpc' => false,
        'wallet' => ['phantom'],
        'chain' => 'solana'
    ],
    11002 => [
        'name' => 'devnet',
        'id' => 11002,
        'token' => 'SOL',
        'full' => 'Solana Devnet',
        'testnet' => true,
        'rpc' => false,
        'wallet' => ['phantom'],
        'chain' => 'solana'
    ],
    // Polygon
    137 => [
        'name' => 'polygon',
        'id' => 137,
        'token' => 'MATIC',
        'full' => 'Polygon',
        'testnet' => false,
        'rpc' => 'https://polygon-mainnet.g.alchemy.com/v2/8r-nRwRsVh-xJuiaT3U179oIHbIKm0mW',
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    80001 => [
        'name' => 'mumbai',
        'id' => 80001,
        'token' => 'MATIC',
        'full' => 'Polygon Mumbai',
        'testnet' => true,
        'rpc' => 'https://polygon-mumbai.g.alchemy.com/v2/4-no0a1q4yrq3-vtoByaqB2qbKdpGrZf',
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    // Fantom
    250 => [
        'name' => 'fantom',
        'id' => 250,
        'token' => 'FTM',
        'full' => 'Fantom',
        'testnet' => false,
        'rpc' => false,
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    4002 => [
        'name' => 'fantom-testnet',
        'id' => 4002,
        'token' => 'FTM',
        'full' => 'Fantom testnet',
        'testnet' => true,
        'rpc' => false,
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    // Avalanche
    43114 => [
        'name' => 'avalanche',
        'id' => 43114,
        'token' => 'AVAX',
        'full' => 'Avalanche',
        'testnet' => false,
        'rpc' => false,
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    43113 => [
        'name' => 'avalanche-testnet',
        'id' => 43113,
        'token' => 'AVAX',
        'full' => 'Avalanche testnet',
        'testnet' => true,
        'rpc' => false,
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    // Optimism
    10 => [
        'name' => 'optimism',
        'id' => 10,
        'token' => 'ETH',
        'full' => 'Optimism',
        'testnet' => false,
        'rpc' => false,
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    420 => [
        'name' => 'optimism-testnet',
        'id' => 420,
        'token' => 'ETH',
        'full' => 'Optimism Goerli Testnet',
        'testnet' => true,
        'rpc' => false,
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    // Arbitrum
    // 42161 => [
    //     'name' => 'arbitrum',
    //     'id' => 42161,
    //     'token' => 'ETH',
    //     'full' => 'Arbitrum',
    //     'testnet' => false,
    //     'rpc' => false,
    //     'wallet' => ['metamask'],
    //     'chain' => 'evm'
    // ],
    // 421611 => [
    //     'name' => 'arbitrum-testnet',
    //     'id' => 421611,
    //     'token' => 'ETH',
    //     'full' => 'Arbitrum testnet',
    //     'testnet' => true,
    //     'rpc' => false,
    //     'wallet' => ['metamask'],
    //     'chain' => 'evm'
    // ],
    // BNB
    56 => [
        'name' => 'binance',
        'id' => 56,
        'token' => 'BNB',
        'full' => 'Binance Smart Chain',
        'testnet' => false,
        'rpc' => false,
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
    97 => [
        'name' => 'binance-testnet',
        'id' => 97,
        'token' => 'tBNB',
        'full' => 'Binance Smart Chain testnet',
        'testnet' => true,
        'rpc' => false,
        'wallet' => ['metamask'],
        'chain' => 'evm'
    ],
];
