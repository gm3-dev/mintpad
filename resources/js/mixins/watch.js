export default {
    data() {
        return {
            tabs: {
                settings: false,
                phases: false,
                collection: false,
                mint: false,
            },
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
        collectionPhases: function(claimPhases) {
            this.tabs.phases = claimPhases.length > 0
        },
        claimPhases: {
            handler: function(val) {
                if (this.buttons.phases === null) {
                    this.buttons.phases = true
                } else {
                    this.buttons.phases = false
                }

                // for (var key in val) {
                //     var phase = val[key]
                //     for (var field in phase) {
                //         var value = phase[field]
                //         if ((field == 'price' || field == 'maxClaimableSupply') && value.length === 0) {
                //             this.claimPhases[key][field] = 0
                //         }
                //         if (field == 'name' && value.length === 0) {
                //             this.claimPhases[key][field] = 'Phase '+(parseInt(key)+1)
                //         }
                //     }
                // }
            },
            deep: true
        },
        collectionNFTs: function(nfts) {
            this.tabs.collection = nfts.length > 0
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
        formCollection: function(oldVal, newVal) {
            if (this.buttons.collection === null) {
                this.buttons.collection = true
            } else {
                this.buttons.collection = false
            }
        },
        formMint: function(oldVal, newVal) {
            if (this.buttons.mint === null) {
                this.buttons.mint = true
            } else {
                this.buttons.mint = false
            }
        },
        tabSettings: function(oldVal, newVal) {
            this.tabs.settings = true
            console.log(typeof oldVal.fee_recipient)
            if (oldVal.name.trim() == '' || oldVal.description.trim() == '' || oldVal.royalties === '' || oldVal.fee_recipient.trim() === '') {
                this.tabs.settings = false
            }
        },
        tabMint: function(oldVal, newVal) {
            this.tabs.mint = true
            if (oldVal.permalink === '') {
                this.tabs.mint = false
            }
        }
    }
}