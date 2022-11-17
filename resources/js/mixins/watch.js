export default {
    data() {
        return {
            buttons: {
                settings: null,
                royalties: null,
                phases: null,
                collection: null,
                mint: null
            }
        }
    },
    watch: {
        claimPhases: {
            handler: function(val) {
                if (this.buttons.phases === null) {
                    this.buttons.phases = true
                } else {
                    this.buttons.phases = false
                }
            },
            deep: true
        },
        collectionChain: async function(chainID) {
            this.hasValidChain = await this.validateMatchingBlockchains(parseInt(chainID))
            if (this.blockchains[parseInt(chainID)]) {
                this.collection.chain = this.blockchains[parseInt(chainID)].chain
            }
        },
        formGeneral: function(oldVal, newVal) {
            if (this.buttons.settings === null) {
                this.buttons.settings = true
            } else {
                this.buttons.settings = false
            }
        },
        formRoyalties: function(oldVal, newVal) {
            if (this.buttons.royalties === null) {
                this.buttons.royalties = true
            } else {
                this.buttons.royalties = false
            }
        },
        formMint: function(oldVal, newVal) {
            if (this.buttons.mint === null) {
                this.buttons.mint = true
            } else {
                this.buttons.mint = false
            }
        }
    }
}