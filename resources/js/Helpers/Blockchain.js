import { Arbitrum, ArbitrumGoerli, Avalanche, AvalancheFuji, Binance, BinanceTestnet, Cmp, CmpTestnet, 
    Ethereum, Fantom, FantomTestnet, Goerli, Mumbai, Optimism, OptimismGoerli, Polygon } from '@thirdweb-dev/chains'
    
export function checkCurrentBlockchain(blockchains, chainId, wallet) {
    const blockchain = blockchains.value[chainId]

    if (!blockchain || wallet.value.account == false) {
        return 'wallet'
    } else if (blockchain.networkId != wallet.value.chainId && wallet.value.name == 'metamask') {
        return 'chain'
    } else {
        return true
    }
}

export function getBlockchains() {
    const mainnets = {
        42161: Arbitrum,
        43114: Avalanche,
        56: Binance,
        256256: Cmp,
        1: Ethereum,
        250: Fantom,
        10: Optimism,
        137: Polygon
    }
    const testnets = {
        421613: ArbitrumGoerli,
        43113: AvalancheFuji,
        97: BinanceTestnet,
        512512: CmpTestnet,
        4002: FantomTestnet,
        5: Goerli,
        80001: Mumbai,
        420: OptimismGoerli
    }
    return {...mainnets, ...testnets}
}

export function getBlockchain(chainId) {
    const blockchains = getBlockchains()
    return blockchains[chainId]
}