import { Ganache, Arbitrum, ArbitrumGoerli, Avalanche, AvalancheFuji, Binance, BinanceTestnet, Cmp, CmpTestnet, Zksync, ZksyncSepoliaTestnet,
    Ethereum, Fantom, FantomTestnet, Sepolia, Mumbai, Optimism, OptimismGoerli, Polygon, Dogechain, DogechainTestnet, Hedera, HederaTestnet,
    LightlinkPhoenix, LightlinkPegasusTestnet, Mantle, MantleTestnet, ShardeumLiberty2X, Base, BaseGoerli,
    ChilizChain, ChilizScovilleTestnet, BobaNetwork, BobaNetworkGoerliTestnet, Cronos, CronosTestnet, KlaytnCypress, KlaytnTestnetBaobab,
    TelosEvm, TelosEvmTestnet, MetalCChain, MetalTahoeCChain, Linea, LineaTestnet, Astar, ConfluxEspace, ConfluxEspaceTestnet, Zetachain,
    ZetachainAthens3Testnet, ScrollSepoliaTestnet, Scroll, AstarZkevm, Zkatana, TaikoKatlaL2, Zora, ZoraTestnet, FormTestnet, BerachainArtio } from '@thirdweb-dev/chains'
// import { TaikoJolnir } from '@/Helpers/CustomBlockchains'

export function checkCurrentBlockchain(blockchains, chainId, wallet) {
    const blockchain = blockchains.value[chainId]

    if (!blockchain || wallet.value == false || wallet.value.account == false) {
        return 'wallet'
    } else if (blockchain.chainId != wallet.value.chainId && wallet.value.name == 'metamask') {
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
    Astar.coingecko = 'astar';
    ConfluxEspace.coingecko = 'conflux-token';

    // Testnet bug?
    MetalTahoeCChain.testnet = true;
    TaikoKatlaL2.testnet = true;

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
        324: Zksync,
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
        381931: MetalCChain,
        592: Astar,
        12611: AstarZkevm,
        1030: ConfluxEspace,
        7000: Zetachain,
        534352: Scroll,
        7777777: Zora,
    }
    const testnets = {
        421613: ArbitrumGoerli,
        43113: AvalancheFuji,
        97: BinanceTestnet,
        512512: CmpTestnet,
        568: DogechainTestnet,
        4002: FantomTestnet,
        11155111: Sepolia,
        80001: Mumbai,
        420: OptimismGoerli,
        300: ZksyncSepoliaTestnet,
        296: HederaTestnet,
        1891: LightlinkPegasusTestnet,
        5001: MantleTestnet,
        59140: LineaTestnet,
        8081: ShardeumLiberty2X,
        84531: BaseGoerli,
        88880: ChilizScovilleTestnet,
        2888: BobaNetworkGoerliTestnet,
        338: CronosTestnet,
        1001: KlaytnTestnetBaobab,
        41: TelosEvmTestnet,
        381932: MetalTahoeCChain,
        7001: ZetachainAthens3Testnet,
        534351: ScrollSepoliaTestnet,
        71: ConfluxEspaceTestnet,
        1261120: Zkatana,
        167008: TaikoKatlaL2,
        999: ZoraTestnet,
        132902: FormTestnet,
        80085: BerachainArtio,
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
