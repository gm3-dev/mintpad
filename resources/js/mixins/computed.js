export default {
    computed: {
        userAddressShort: function() {
            return this.wallet.account ? this.wallet.account.substring(0, 5)+'...'+this.wallet.account.substr(this.wallet.account.length - 3) : ''
        },
        collectionChain() {
            return this.collection.chain_id
        },
        collectionNFTs() {
            return this.collection.nfts
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
        formCollection() {
            return {nfts: this.collection.nfts}
        },
        formMint() {
            return {permalink: this.collection.permalink, title: this.collection.seo.title, description: this.collection.seo.description, image: this.collection.seo.image}
        },
        tabSettings() {
            return {
                name: this.collection.name, 
                description: this.collection.description,
                royalties: this.collection.royalties,
                fee_recipient: this.collection.fee_recipient
            }
        },
        tabMint() {
            return {
                permalink: this.collection.permalink,
            }
        }
    }
}