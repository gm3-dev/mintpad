import { ThirdwebSDK } from '@thirdweb-dev/sdk'
import { ThirdwebSDK as ThirdwebSolanaSDK } from "@thirdweb-dev/sdk/solana"

export default {
    methods: {
        /**
         * Set SDK object
         * @param {string} blockchain blockchain name
         */
        setSDK: function(chainID) {
            var chain = this.blockchains[chainID]
            var blockchain = this.getSDKBlockchain(chain)
            this.sdk = new ThirdwebSDK(blockchain)
        },
        /**
         * Set SDK object
         * @param {string} signer wallet signer
         * @param {string} blockchain blockchain name
         */
        setSDKFromSigner: function(signer, chainID) {
            var chain = this.blockchains[chainID]
            console.log('chain', chain)
            var blockchain = this.getSDKBlockchain(chain)
            console.log('blockchain', blockchain)
            console.log('chain.chain', chain.chain)

            // If EVM chain
            if (chain.chain == 'evm') {
                this.sdk = ThirdwebSDK.fromSigner(signer, blockchain, {})
            }

            // If Solana chain
            if (chain.chain == 'solana') {
                this.sdk = ThirdwebSolanaSDK.fromNetwork(blockchain)
                this.sdk.wallet.connect(signer)
            }
        },
        /**
         * Get chain RPC or chain name
         * @param {object} chain 
         * @returns string
         */
        getSDKBlockchain: function(chain) {
            return chain.rpc !== false ? chain.rpc : chain.name
        },
        /**
         * Set smart contract object
         * @param {string} address wallet address
         */
        setSmartContract: async function(address) {
            this.contract = await this.getNFTDrop(address)
        },
    }
}