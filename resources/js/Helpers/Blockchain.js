import { Ganache, Arbitrum, ArbitrumGoerli, Avalanche, AvalancheFuji, Binance, BinanceTestnet, Cmp, CmpTestnet, Zksync, ZksyncSepoliaTestnet,
    Ethereum, Fantom, FantomTestnet, Sepolia, Mumbai, Optimism, OptimismGoerli, Polygon, Dogechain, DogechainTestnet, Hedera, HederaTestnet,
    LightlinkPhoenix, LightlinkPegasusTestnet, Mantle, MantleTestnet, Base, BaseGoerli, ArtheraTestnet, Arthera, MintTestnet, Mint, Vanar, NeonEvm, Opbnb, Vanguard, NeonEvmDevnet,
    ChilizChain, ChilizScovilleTestnet, BobaNetwork, BobaNetworkGoerliTestnet, Cronos, CronosTestnet, KlaytnCypress, KlaytnTestnetBaobab, Taiko, BerachainBartio, HychainTestnet, SuperseedSepoliaTestnet,
    TelosEvm, TelosEvmTestnet, MetalCChain, MetalTahoeCChain, Linea, LineaSepolia, Astar, ConfluxEspace, ConfluxEspaceTestnet, Zetachain, DegenChain, Hychain, Inevm, Shibarium, TaikoHeklaL2,
    ZetachainAthens3Testnet, ScrollSepoliaTestnet, Scroll, AstarZkevm, Zkatana, TaikoKatlaL2, Zora, ZoraSepoliaTestnet, FormTestnet, BerachainArtio, BlastSepoliaTestnet, BlastBlastmainnet, ShardeumSphinx1X, Rari, RarichainTestnet, VictionTestnet, Viction } from '@thirdweb-dev/chains'
import {WeaveEVMTestnet} from '@/Helpers/CustomBlockchains'
import {ApechainCurtis} from '@/Helpers/CustomBlockchains'
import {ApechainMainnet} from '@/Helpers/CustomBlockchains'
import { ShapeMainnet } from '@/Helpers/CustomBlockchains'
import { ShapeSepoliaTestnet } from '@/Helpers/CustomBlockchains'
// import { AbstractTestnet } from '@/Helpers/CustomBlockchains'

// import { TaikoJolnir } from '@/Helpers/CustomBlockchains'
// import {BlastL2} from "@/Helpers/CustomBlockchains";

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
    // See here for list of token id's to use https://docs.coingecko.com/reference/simple-price
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
    LightlinkPhoenix.coingecko = 'ethereum'
    Mantle.coingecko = 'mantle';
    Base.coingecko = 'ethereum'
    Linea.coingecko = 'ethereum';
    ChilizChain.coingecko = 'chiliz';
    BobaNetwork.coingecko = 'boba-network';
    Cronos.coingecko = 'crypto-com-chain';
    KlaytnCypress.coingecko = 'klay-token';
    TelosEvm.coingecko = 'telos';
    MetalCChain.coingecko = 'metal-blockchain';
    Astar.coingecko = 'astar';
    AstarZkevm.coingecko = 'ethereum'
    ConfluxEspace.coingecko = 'conflux-token';
    Zetachain.coingecko = 'zetachain'
    Zora.coingecko = 'ethereum'
    // BlastL2.coingecko = 'ethereum'
    BlastBlastmainnet.coingecko = 'ethereum'
    Rari.coingecko = 'ethereum'
    DegenChain.coingecko = 'degen-base'
    Hychain.coingecko = 'hytopia'
    Inevm.coingecko = 'injective-protocol'
    Shibarium.coingecko = 'bone-shibaswap'
    Scroll.coingecko = 'ethereum'
    Zksync.coingecko = 'ethereum'
    Mint.coingecko = 'ethereum'
    Vanar.coingecko = 'vanar-chain'
    Vanguard.coingecko = 'vanar-chain'
    NeonEvm.coingecko = 'neon'
    NeonEvmDevnet.coingecko = 'neon'
    Opbnb.coingecko = 'binancecoin'
    Taiko.coingecko = 'ethereum'
    Viction.coingecko='tomochain'
    ApechainMainnet.coingecko='apecoin'
    ShapeMainnet.coingecko='ethereum'


    // Testnet bug?
    MetalTahoeCChain.testnet = true;
    TaikoKatlaL2.testnet = true;
    TaikoHeklaL2.testnet = true;
    ShardeumSphinx1X.testnet = true;
// AbstractTestnet.testnet= false;
    Hychain.testnet = false;
    ShapeSepoliaTestnet.testnet=true;
    ShapeMainnet.testnet=true;



    //block explorer custom overwrite
    ConfluxEspace.explorers = [{
        "name": "Conflux Scan",
        "url": "https://evm.confluxscan.io",
        "standard": "none"
    }];

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
        3776: AstarZkevm,
        1030: ConfluxEspace,
        7000: Zetachain,
        534352: Scroll,
        7777777: Zora,
        81457: BlastBlastmainnet,
        1380012617: Rari,
        10242: Arthera, // TODO: add token once it is launched on coingecko
        666666666: DegenChain,
        2911: Hychain,
        2525: Inevm,
        109: Shibarium,
        185: Mint,
        2040: Vanar,
        245022934: NeonEvm,
        204: Opbnb,
        167000: Taiko,
        88: Viction,
        33139: ApechainMainnet,
        360: ShapeMainnet
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
        59141: LineaSepolia,
        8082: ShardeumSphinx1X,
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
        167009: TaikoHeklaL2,
        999999999: ZoraSepoliaTestnet,
        132902: FormTestnet,
        80085: BerachainArtio,
        80084: BerachainBartio,
        168587773: BlastSepoliaTestnet,
        1918988905: RarichainTestnet,
        10243: ArtheraTestnet,
        1686: MintTestnet,
        78600: Vanguard,
        245022926: NeonEvmDevnet,
        29112: HychainTestnet,
        53302: SuperseedSepoliaTestnet,
        9496: WeaveEVMTestnet,
        33111: ApechainCurtis,
        89:VictionTestnet,
        11011: ShapeSepoliaTestnet,
        // 11124:AbstractTestnet
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
