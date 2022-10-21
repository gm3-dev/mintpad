// Includes
import { initSentry, resportError } from './sentry'

export default {
    methods: {
        deployNFTDrop: async function() {
            this.setSDKFromSigner(this.wallet.signer, this.collection.chain_id)

            var parameters = {
                name: this.collection.name,
                symbol: this.collection.symbol,
                description: this.collection.description,
                primary_sale_recipient: this.wallet.account, // primary sales
                fee_recipient: this.wallet.account, // royalties address
                seller_fee_basis_points: this.collection.royalties * 100, // royalties address
                platform_fee_recipient: '0x892a99573583c6490526739bA38BaeFae10a84D4', // platform fee address
                platform_fee_basis_points: 500, // platform fee (5%)
                totalSupply: this.collection.totalSupply // Solana only
            }
            
            var contractAddress = false
            try {
                // Deploy on EVM chain
                if (this.collection.chain == 'evm') {
                    contractAddress = await this.sdk.deployer.deployNFTDrop(parameters)
                }

                // Deploy on Solana chain
                if (this.collection.chain == 'solana') {
                    contractAddress = await this.sdk.deployer.createNftDrop(parameters)
                }

            } catch (error) {
                resportError(error)
            }

            return contractAddress
        },
        getNFTDrop: async function(address) {
            var contract = false
            try {
                // Set from EVM chain
                if (this.collection.chain == 'evm') {
                    contract = await this.sdk.getNFTDrop(address)
                }

                // Set from Solana chain
                if (this.collection.chain == 'solana') { 
                    contract = await this.sdk.getProgram(address, "nft-drop")
                }

            } catch (error) {
                console.log(error)
                resportError(error)
            }

            return contract
        },
        getMetadata: async function(address) {
            var metadata = false
            try {
                // Get from EVM chain
                if (this.collection.chain == 'evm') {
                    metadata = await this.contract.metadata.get()
                }

                // Get from Solana chain
                if (this.collection.chain == 'solana') { 
                    metadata = await this.contract.getMetadata()
                }

            } catch (error) {
                console.log(error)
                resportError(error)
            }

            return metadata
        },
        getClaimPhases: async function() {
            var claimConditions = false
            try {
                // Get from EVM chain
                if (this.collection.chain == 'evm') {
                    claimConditions = await this.contract.claimConditions.getAll()
                }

                // Get from Solana chain
                if (this.collection.chain == 'solana') { 
                    const claimCondition = await this.contract.claimConditions.get()
                    console.log(claimCondition)
                    if (claimCondition.price != undefined) {
                        claimConditions = [claimCondition]
                    }
                }

            } catch (error) {
                console.log(error)
                resportError(error)
            }

            return claimConditions
            
        }
    }
}