import { ThirdwebSDK } from '@thirdweb-dev/sdk'

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
            var blockchain = this.getSDKBlockchain(chain)
            this.sdk = ThirdwebSDK.fromSigner(signer, blockchain, {})
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
            this.contract = await this.sdk.getNFTDrop(address)
        },
    }
}