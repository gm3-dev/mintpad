export default {
    data() {
        return {}
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
            } else {
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
            console.log(this.collection)
            console.log(this.claimPhases)

            for (var i = 0; i < this.claimPhases.length; i++) {
                var claimPhase = this.claimPhases[i]
                // var newClaimPhase = {
                //     startTime: new Date(claimPhase.startTime),
                //     price: claimPhase.price,
                //     maxClaimableSupply: claimPhase.maxClaimableSupply == 0 ? 'unlimited' : claimPhase.maxClaimableSupply,
                //     quantityLimitPerTransaction: 1,
                //     waitInSeconds: claimPhase.waitInSeconds == 0 ? ethers.constants.MaxUint256 : 5,
                //     snapshot: claimPhase.whitelist == 0 ? [] : claimPhase.snapshot,
                // }
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
        }
    }
}