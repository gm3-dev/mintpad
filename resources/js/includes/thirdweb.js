import { ThirdwebSDK } from '@thirdweb-dev/sdk'
import { Cmp, CmpTestnet } from '@thirdweb-dev/chains'
// import { ThirdwebSDK as ThirdwebSolanaSDK } from "@thirdweb-dev/sdk/solana"

export default {
    methods: {
        setSDK: function(chainID) {
            var chain = this.blockchains[chainID]
            var blockchain = this.getSDKBlockchain(chain)

            return new ThirdwebSDK(blockchain)
        },
        getSDKFromSigner: function(signer, chainID) {
            var chain = this.blockchains[chainID]
            var blockchain = this.getSDKBlockchain(chain)

            // If EVM chain
            if (chain.chain == 'evm') {
                return ThirdwebSDK.fromSigner(signer, blockchain, {})
            }

            // If Solana chain
            // if (chain.chain == 'solana') {
            //     this.sdk = ThirdwebSolanaSDK.fromNetwork(blockchain)
            //     this.sdk.wallet.connect(signer)
            // }

            return false;
        },
        getSmartContract: async function(chainID, address) {
            const sdk = this.setSDK(chainID)
            return await sdk.getContract(address, 'nft-drop')
        },
        getSmartContractFromSigner: async function(signer, chainID, address) {
            const sdk = this.getSDKFromSigner(signer, chainID)
            return await sdk.getContract(address, 'nft-drop')
        },
        getSDKBlockchain: function(chain) {
            if (chain.name == 'CMP-Mainnet') {
                return Cmp
            } else if (chain.name == 'CMP-Testnet') {
                return CmpTestnet
            } else {
                return chain.rpc !== false ? chain.rpc : chain.id
            }
        }
    }
}