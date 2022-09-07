import { ethers } from 'ethers'
import { ThirdwebSDK } from '@thirdweb-dev/sdk'

export async function initFastSigner() {
    const networks = {
        
    }
    // console.log(ethers.providers)
    // const provider = new ethers.providers.AlchemyProvider("maticmum", '4-no0a1q4yrq3-vtoByaqB2qbKdpGrZf')
    // console.log(provider.getUrl('maticmum', '4-no0a1q4yrq3-vtoByaqB2qbKdpGrZf'))
    // const provider = new ethers.providers.EtherscanProvider("rinkeby", 'QU6D7VYHMWCD89IFCZD6S19P8R69IBCU6D')
    // const provider2 = new ethers.providers.JsonRpcProvider(provider.getUrl('maticmum', '4-no0a1q4yrq3-vtoByaqB2qbKdpGrZf'))

    // console.log(provider2.getSigner())
    // const network = 'maticmum'
    const network = {
        name: 'maticmum',
        chainId: 80001,
        _defaultProvider: (providers) => new providers.JsonRpcProvider('https://polygon-mumbai.g.alchemy.com/v2/4-no0a1q4yrq3-vtoByaqB2qbKdpGrZf')
    }
    // const network = {
    //     name: 'rinkeby',
    //     chainId: 4,
    //     _defaultProvider: (providers) => new providers.JsonRpcProvider('https://eth-rinkeby.alchemyapi.io/v2/RkXYi5a_0aOzm_6W4VUkvKJw3TgQcGOL')
    // };
    // const network = 'rinkeby'
    const provider = await new ethers.getDefaultProvider(network, {
        // alchemy: '4-no0a1q4yrq3-vtoByaqB2qbKdpGrZf', // Maticmum
        // alchemy: 'RkXYi5a_0aOzm_6W4VUkvKJw3TgQcGOL', // Rinkeby
    });

    return provider.getSigner()

    // console.log(provider)
    // console.log(provider.getUrl())
    // console.log(provider.getSigner())
    // console.log(provider.getUrl('hi', 'go'))

    // const sdk = ThirdwebSDK.fromSigner(provider.getSigner(), 'mumbai', {})
    // // const contract = await sdk.getNFTDrop('0xdeAfA4be5b0Ca4cC2154A0B26C236CE0F9d1303F') // mumbai
    // const contract = await sdk.getNFTDrop('0x0663CC3402234Db5706bf756097D75396e1c0bdf') // rinkeby
    // try {
    //     const metadata = await contract.metadata.get()
    //     const royalties = await contract.royalties.getDefaultRoyaltyInfo()

    //     console.log(metadata)
    //     console.log(royalties)
    // } catch (e) {
    //     console.log(e)
    // }

    // return true;
}