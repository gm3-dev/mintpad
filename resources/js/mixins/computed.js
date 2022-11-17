export default {
    computed: {
        userAddressShort: function() {
            return this.wallet.account ? this.wallet.account.substring(0, 5)+'...'+this.wallet.account.substr(this.wallet.account.length - 3) : ''
        },
        collectionChain() {
            return this.collection.chain_id
        },
        collectionPhases() {
            return this.claimPhases
        },
        formGeneral() {
            return {name: this.collection.name, description: this.collection.description}
        },
        formRoyalties() {
            return {fee_recipient: this.collection.fee_recipient, royalties: this.collection.royalties}
        },
        formMint() {
            return {permalink: this.collection.permalink, title: this.collection.seo.title, description: this.collection.seo.description, image: this.collection.seo.image}
        }
    }
}