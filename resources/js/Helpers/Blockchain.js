import { Ganache, Arbitrum, ArbitrumGoerli, Avalanche, AvalancheFuji, Binance, BinanceTestnet, Cmp, CmpTestnet, ZksyncEra, ZksyncEraTestnet,
    Ethereum, Fantom, FantomTestnet, Goerli, Mumbai, Optimism, OptimismGoerli, Polygon, Dogechain, DogechainTestnet, Hedera, HederaTestnet,
    LightlinkPhoenix, LightlinkPegasusTestnet, Mantle, MantleTestnet, ShardeumSphinxDapp1X, TaikoGrimsvotnL2, Base, BaseGoerli,
    ChilizChain, ChilizScovilleTestnet, BobaNetwork, BobaNetworkGoerliTestnet, Cronos, CronosTestnet, KlaytnCypress, KlaytnTestnetBaobab,
    TelosEvm, TelosEvmTestnet, MetalCChain, MetalTahoeCChain, Linea, LineaTestnet } from '@thirdweb-dev/chains'
import { TaikoJolnir } from '@/Helpers/CustomBlockchains'
    
export function checkCurrentBlockchain(blockchains, chainId, wallet) {
    const blockchain = blockchains.value[chainId]

    if (!blockchain || wallet.value == false || wallet.value.account == false) {
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
    // LightlinkPhoenix.coingecko = '';
    Mantle.coingecko = 'mantle';
    // Base.coingecko = '';
    // Linea.coingecko = '';
    ChilizChain.coingecko = 'chiliz';
    BobaNetwork.coingecko = 'boba-network';
    Cronos.coingecko = 'crypto-com-chain';
    KlaytnCypress.coingecko = 'klay-token';
    TelosEvm.coingecko = 'telos';
    MetalCChain.coingecko = 'metal-blockchain';

    // Testnet bug?
    MetalTahoeCChain.testnet = true;

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
        324: ZksyncEra,
        295: Hedera,
        1890: LightlinkPhoenix,
        5000: Mantle,
        59144: Linea,
        8453: Base,
        88888: ChilizChain,
        288: BobaNetwork,
        25: Cronos,
        8217: KlaytnCypress,
        40: TelosEvm,
        381931: MetalCChain
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
        280: ZksyncEraTestnet,
        296: HederaTestnet,
        1891: LightlinkPegasusTestnet,
        5001: MantleTestnet,
        59140: LineaTestnet,
        8081: ShardeumSphinxDapp1X,
        167005: TaikoGrimsvotnL2,
        167007: TaikoJolnir,
        84531: BaseGoerli,
        88880: ChilizScovilleTestnet,
        2888: BobaNetworkGoerliTestnet,
        338: CronosTestnet,
        1001: KlaytnTestnetBaobab,
        41: TelosEvmTestnet,
        381932: MetalTahoeCChain
    }
    // testnets[1337] = Ganache
    // testnets[1337].chainId = 1337
    // testnets[1337].networkId = 1337
    // testnets[1337].rpc = [
    //     'HTTP://127.0.0.1:7545'
    // ];    
    // testnets[1337].explorers = [{
    //     "name": "ganache",
    //     "url": "https://www.ganache.com",
    //     "standard": "EIP3091"
    // }]

    return {...mainnets, ...testnets}
}

export function getBlockchain(chainId) {
    const blockchains = getBlockchains()
    return blockchains[chainId]
}