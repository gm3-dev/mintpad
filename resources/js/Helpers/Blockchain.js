import { Arbitrum, ArbitrumGoerli, Avalanche, AvalancheFuji, Binance, BinanceTestnet, Cmp, CmpTestnet, ZksyncEra, ZksyncEraTestnet,
    Ethereum, Fantom, FantomTestnet, Goerli, Mumbai, Optimism, OptimismGoerli, Polygon, Dogechain, DogechainTestnet, Hedera, HederaTestnet,
    LightlinkPhoenix, LightlinkPegasusTestnet } from '@thirdweb-dev/chains'
    
export function checkCurrentBlockchain(blockchains, chainId, wallet) {
    const blockchain = blockchains.value[chainId]

    if (!blockchain || wallet.value.account == false || wallet.value == false) {
        return 'wallet'
    } else if (blockchain.networkId != wallet.value.chainId && wallet.value.name == 'metamask') {
        return 'chain'
    } else {
        return true
    }
}

export function getBlockchains() {
    Cmp.name = 'Caduceus'
    CmpTestnet.name = 'Caduceus Testnet'

    // Set CoinGecko Token IDs
    Arbitrum.coingecko = 'arbitrum';
    Avalanche.coingecko = 'avalanche-2';
    Binance.coingecko = 'binancecoin';
    Cmp.coingecko = 'caduceus';
    Dogechain.coingecko = 'dogecoin';
    Ethereum.coingecko = 'ethereum';
    Fantom.coingecko = 'fantom';
    Optimism.coingecko = 'optimism';
    Polygon.coingecko = 'matic-network';
    // ZksyncEra.coingecko = '';
    Hedera.coingecko = 'hedera-hashgraph';

    const mainnets = {
        42161: Arbitrum,
        43114: Avalanche,
        56: Binance,
        256256: Cmp,
        2000: Dogechain,
        1: Ethereum,
        250: Fantom,
        10: Optimism,
        137: Polygon,
        // 324: ZksyncEra,
        295: Hedera,
        1890: LightlinkPhoenix
    }
    const testnets = {
        421613: ArbitrumGoerli,
        43113: AvalancheFuji,
        97: BinanceTestnet,
        512512: CmpTestnet,
        568: DogechainTestnet,
        4002: FantomTestnet,
        5: Goerli,
        80001: Mumbai,
        420: OptimismGoerli,
        // 280: ZksyncEraTestnet,
        296: HederaTestnet,
        1891: LightlinkPegasusTestnet
    }
    return {...mainnets, ...testnets}
}

export function getBlockchain(chainId) {
    const blockchains = getBlockchains()
    return blockchains[chainId]
}