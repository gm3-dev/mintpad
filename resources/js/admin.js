window.$ = require('jquery')
import { createApp } from 'vue'
import Alpine from 'alpinejs'
import VueTippy from "vue-tippy"

// Includes
import { initSentry, resportError } from './includes/sentry'
import metamask from './wallets/metamask.js'
import helpers from './includes/helpers.js'
import modal from './includes/modal.js'
import thirdweb from './includes/thirdweb.js'

// Config
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
window.Alpine = Alpine
Alpine.start()

if (document.getElementById('app')) {
    
    const app = createApp({
        mixins: [metamask,helpers,modal,thirdweb],
        data() {
            return {
                collectionID: false,
                sdk: false,
                contract: false,
                contractAddress: false,
                chainData: false,
                collection: { nfts: [] }
            }
        },
        async mounted() {    
            await this.setBlockchains()
            await this.initMetaMask(false)
            this.appReady()
        },
        methods: {
            openCollectionModal: function(collectionID) {
                this.setCollectionData(collectionID)
            },
            setCollectionData: async function(collectionID) {
                axios.get('/collections/'+collectionID+'/fetch').then(async (response) => {
                    if (!await this.validateMatchingBlockchains(response.data.chain_id)) {
                        await this.switchBlockchainTo(response.data.chain_id)
                        return
                    }

                    this.collection = { nfts: [] }
                    // Set DB data
                    this.chainData = this.blockchains[response.data.chain_id]
                    this.contractAddress = response.data.address
                    this.collection.name = response.data.name
                    this.collection.chain_id = response.data.chain_id
                    this.collection.chain = this.blockchains[this.collection.chain_id].chain
                    this.collection.token = response.data.token
                    this.hasValidChain = await this.validateMatchingBlockchains(this.collection.chain_id)
                    this.collection.seo = response.data.seo

                    this.modalToggle(true)

                    // Mint settings
                    this.collection.permalink = response.data.permalink

                    const contract = await this.getSmartContractFromSigner(this.wallet.signer, this.collection.chain_id, this.contractAddress)
                    try {
                        // Global settings
                        const metadata = await contract.metadata.get()
                        const platformFees = await contract.platformFees.get()
                        this.collection.primary_sales_recipient = await contract.sales.getRecipient()
                        this.collection.platform_fee = platformFees.platform_fee_basis_points / 100
                        this.collection.platform_fee_recipient = platformFees.platform_fee_recipient
                        this.collection.name = metadata.name
                        this.collection.description = metadata.description
                        const royalties = await contract.royalties.getDefaultRoyaltyInfo()
                        this.collection.fee_recipient = royalties.fee_recipient
                        this.collection.royalties = royalties.seller_fee_basis_points / 100

                        // Claim phases
                        var claimConditions = await contract.claimConditions.getAll({withAllowList: false})
                        this.claimPhases = this.parseClaimConditions(claimConditions, response.data)

                        // Collection
                        this.collection.totalSupply = await contract.totalSupply()
                        this.collection.totalClaimedSupply = await contract.totalClaimedSupply()
                        this.collection.totalRatio = Math.round((this.collection.totalClaimedSupply/this.collection.totalSupply)*100)
                        this.collection.nfts = await contract.getAll({count: 8})
                    } catch (error) {
                        resportError(error)
                        this.setMessage('Contract could not be loaded, please try again.', 'error', true)
                    }
                })
            },
            deleteCollection: function(collectionID) {
                if (confirm("Are you sure you want to delete this collection?") == true) {

                    axios.delete('/admin/collections/'+collectionID).then((response) => {
                        window.location.reload()
                    }).catch((error) => {
                        console.log(error)
                    })
                }
            }
        }
    })

    initSentry(app)
    app.component('dark-mode', require('./components/DarkMode.vue').default)
        .component('dropdown', require('./components/Dropdown.vue').default)
        .component('dropdown-link', require('./components/DropdownLink.vue').default)
        .component('hamburger-menu', require('./components/HamburgerMenu.vue').default)
        .component('hamburger-menu-link', require('./components/HamburgerMenuLink.vue').default)
    app.use(
        VueTippy,
        {
            directive: 'tippy', // => v-tippy
            component: 'tippy', // => <tippy/>
        }
    )
    app.mount('#app')
}