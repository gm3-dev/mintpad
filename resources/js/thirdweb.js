import { ThirdwebSDK } from '@thirdweb-dev/sdk'

export default {
    methods: {
        /**
         * Set SDK object
         * @param {string} signer wallet signer
         * @param {string} blockchain blockchain name
         */
        setSDK: function(signer, blockchain) {
            this.sdk = ThirdwebSDK.fromSigner(signer, blockchain, {})
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