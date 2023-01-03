export default {
    data() {
        return {
            tabs: {
                settings: false,
                phases: false,
                collection: false,
                mint: false,
            },
        }
    },
    methods: {
        validateDeployContract: function() {
            if (this.collection.royalties.length < 1) {
                return this.setValidationResponse(false, 'Creator royalties must be a number')
            } else if (this.collection.royalties < 0 || this.collection.royalties > 100) {
                return this.setValidationResponse(false, 'Creator royalties must be a number between 0 and 100')
            } else if (this.collection.symbol.length < 2) {
                return this.setValidationResponse(false, 'Symbol / ticker must be at least 2 characters long')
            } else if (this.collection.name.length < 3) {
                return this.setValidationResponse(false, 'Collection name must be at least 3 characters long')
            } else if (this.collection.description.length < 3) {
                return this.setValidationResponse(false, 'Collection description must be at least 3 characters long')
            }  else {
                return this.setValidationResponse(true, 'Valid collection data')
            }
        },
        validateUpdateMetadata: function() {
            if (this.collection.name.length < 3) {
                return this.setValidationResponse(false, 'Collection name must be at least 3 characters long')
            } else {
                return this.setValidationResponse(true, 'Valid collection data')
            }
        },
        validateUpdateRoyalties: function() {
            if (this.collection.royalties.length < 1) {
                return this.setValidationResponse(false, 'Creator royalties must be a number')
            } else if (this.collection.royalties < 0 || this.collection.royalties > 100) {
                return this.setValidationResponse(false, 'Creator royalties must be a number between 0 and 100')
            } else if (this.collection.fee_recipient.length < 10) {
                return this.setValidationResponse(false, 'Recipient address is not valid')
            } else {
                return this.setValidationResponse(true, 'Valid collection data')
            }
        },
        validateUpdateClaimPhases: function() {
            for (var i = 0; i < this.claimPhases.length; i++) {
                var claimPhase = this.claimPhases[i]
                
                if (claimPhase.name.length < 1) {
                    return this.setValidationResponse(false, 'Phase '+ claimPhase.id +': Mint phase name must be at least 1 character long')
                } else if (claimPhase.maxClaimableSupply.length < 1) {
                    return this.setValidationResponse(false, claimPhase.name +': Number of NFTs must be a number')
                } else if (claimPhase.maxClaimableSupply < 0) {
                    return this.setValidationResponse(false, claimPhase.name +': Number of NFTs is not valid')
                } else if (claimPhase.price.length < 1) {
                    return this.setValidationResponse(false, claimPhase.name +': Mint price must be a number')
                } else if (claimPhase.price < 0) {
                    return this.setValidationResponse(false, claimPhase.name +': Mint price is not valid')
                }
            }

            return this.setValidationResponse(true, 'Valid collection data')
        },
        setValidationResponse: function(valid, message) {
            return {valid: valid, message: message}
        },
        /**
         * Tab validation
         */
        validateTabStatus: function() {
            this.validateTabSettings()
            this.validateTabClaimPhases()
            this.validateTabCollection()
            this.validateTabMintPage()
        },
        validateTabSettings: function() {
            this.tabs.settings = true
            if (this.collection.name.trim() == '' || this.collection.description.trim() == '' || this.collection.royalties === '' || this.collection.fee_recipient.trim() === '') {
                this.tabs.settings = false
            }
        },
        validateTabClaimPhases: function() {
            this.tabs.phases = this.claimPhases.length > 0
        },
        validateTabCollection: function() {
            this.tabs.collection = this.collection.nfts.length > 0
        },
        validateTabMintPage: function() {
            this.tabs.mint = true
            if (this.collection.permalink.trim() === '' || this.collection.seo.title.trim() === '' || this.collection.seo.description.trim() === '') {
                this.tabs.mint = false
            }
        },
    }
}