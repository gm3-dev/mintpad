window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'
import { ethers } from 'ethers'
import Alpine from 'alpinejs'
import VueTippy, { TippyComponent } from "vue-tippy"

// Mixins
import computed from './mixins/computed.js'
import watch from './mixins/watch.js'

// Includes
import { eventBus } from './includes/event-bus'
import { initSentry, resportError } from './includes/sentry'
import wallet from './includes/wallet.js'
import metamask from './wallets/metamask.js'
import phantom from './wallets/phantom.js'
import helpers from './includes/helpers.js'
import modal from './includes/modal.js'
import resources from './includes/resources'
import thirdweb from './includes/thirdweb.js'
import thirdwebWrapper from './includes/thirdweb-wrapper.js'
import nftgenerator from './includes/nft-generator.js'
import validation from './includes/validation.js'

// Config
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
window.Alpine = Alpine
Alpine.start()
Vue.use(VueTippy)
Vue.component("tippy", TippyComponent)
initSentry(Vue)

Vue.directive('closable', {
    bind (el, binding, vnode) {
        // Here's the click/touchstart handler
        // (it is registered below)
        el.clickOutsideEvent = (e) => {
            e.stopPropagation()
            
            const { handler, exclude } = binding.value
            let clickedOnExcludedEl = false
            exclude.forEach(refName => {
                if (!clickedOnExcludedEl) {
                    const excludedEl = vnode.context.$refs[refName]
                    clickedOnExcludedEl = excludedEl.contains(e.target)
                }
            })
            if (!el.contains(e.target) && !clickedOnExcludedEl) {
                vnode.context[handler]()
            }
        }
        // Register click/touchstart event listeners on the whole page
        document.addEventListener('click', el.clickOutsideEvent)
        document.addEventListener('touchstart', el.clickOutsideEvent)
    },
    unbind (el) {
        // If the element that has v-closable is removed, then
        // unbind click/touchstart listeners from the whole page
        document.removeEventListener('click', el.clickOutsideEvent)
        document.removeEventListener('touchstart', el.clickOutsideEvent)
    }
})

if (document.getElementById('app')) {
    Vue.component('tinymce', require('./components/TinyMCE.vue').default);
    Vue.component('dark-mode', require('./components/DarkMode.vue').default);
    Vue.component('connect-wallet', require('./components/ConnectWallet.vue').default);
    Vue.component('wallet-manager', require('./components/WalletManager.vue').default);
    Vue.component('dropdown', require('./components/Dropdown.vue').default);
    Vue.component('dropdown-link', require('./components/DropdownLink.vue').default);
    Vue.component('hamburger-menu', require('./components/HamburgerMenu.vue').default);
    Vue.component('hamburger-menu-link', require('./components/HamburgerMenuLink.vue').default);
    Vue.component('status-button', require('./components/StatusButton.vue').default);

    new Vue({
        el: '#app',
        mixins: [computed, watch, resources, validation, wallet, metamask, phantom, helpers, modal, thirdweb, thirdwebWrapper, nftgenerator],
        data: {
            forms: {},
            collectionID: false,
            sdk: false,
            contract: false,
            contractAddress: false,
            upload: false,
            collection: {
                name: '',
                chain_id: 1,
                chain: 'evm',
                token: 'ETH',
                symbol: '',
                fee_recipient: '',
                royalties: 0,
                description: '',
                metadata: [],
                nfts: [],
                previews: [],
                totalSupply: 0,
                totalClaimedSupply: 0,
                permalink: '',
                mintUrl: '',
                editorUrl: '',
                fullPermalink: '',
                seo: {},
            },
            claimPhases: [],
            claimPhaseInfo: [],
            page: {
                name: '',
                tab: 0,
            },
            validation: {}
        },
        async mounted() {
            if ($('#collectionID').length) {
                this.collectionID = $('#collectionID').val()
            }
            
            await this.setBlockchains()

            // Listen to connect button
            eventBus.$on('connect-wallet', async (wallet) =>{
                await this.connectWallet(wallet)
            });

            // Check chosen wallet
            if (localStorage.getItem('walletName')) {
                await this.initWallet(localStorage.getItem('walletName'))
            }

            this.appReady()
            this.setWalletUI()
            this.setPage()
            this.setPageData()
            this.setResource('social-sharing')
        },
        methods: {
            changeEditTab: async function(tab) {
                this.page.tab = tab
            },
            nextEditTab: async function() {
                if (this.page.tab == 4) {
                    //
                } else {
                    this.page.tab++
                }
            },
            previousEditTab: async function() {
                if (this.page.tab == 1) {
                    //
                } else {
                    this.page.tab--
                }
            },
            setPage: function() {
                this.page.name = this.$el.getAttribute('data-page')
            },
            setPageData: async function() {

                // Collection pages
                if (this.page.name == 'collections.create') {
                    if (this.blockchains[this.wallet.network.id]) {
                        this.collection.chain_id = this.wallet.network.id
                    }
                    this.hasValidChain = await this.validateMatchingBlockchains(this.collection.chain_id)

                } else if (this.page.name == 'collections.edit') {
                    this.setClaimPhasesInfo()
                    await this.setCollectionData()
                }
            },
            setCollectionData: async function() {
                axios.get('/collections/'+this.collectionID+'/fetch').then(async (response) => {
                    // Set DB data
                    this.contractAddress = response.data.address
                    this.collection.chain_id = response.data.chain_id
                    this.collection.chain = this.blockchains[this.collection.chain_id].chain
                    this.collection.token = response.data.token
                    this.hasValidChain = await this.validateMatchingBlockchains(this.collection.chain_id)
                    this.collection.seo = response.data.seo

                    // Mint settings
                    this.collection.permalink = response.data.permalink
                    this.collection.mintUrl = response.data.mint_url
                    this.collection.editorUrl = response.data.editor_url
                    this.collection.fullMintUrl = this.collection.mintUrl+'/'+this.collection.permalink
                    this.collection.fullEditorUrl = this.collection.editorUrl+'/'+this.collection.permalink

                    // Check if wallet is connected to the correct blockchain
                    if (this.hasValidChain !== true) {
                        this.page.tab = -1
                        this.setMessage('There is a problem with your wallet', 'error', true)
                        return;
                    } else {
                        this.page.tab = 1
                    }

                    this.setSDKFromSigner(this.wallet.signer, this.collection.chain_id)
                    await this.setSmartContract(this.contractAddress)

                    try {
                        // Global settings
                        const metadata = await this.getMetadata()
                        this.collection.name = metadata.name
                        this.collection.description = metadata.description
                        const royalties = await this.contract.royalties.getDefaultRoyaltyInfo()
                        this.collection.fee_recipient = royalties.fee_recipient
                        this.collection.royalties = royalties.seller_fee_basis_points / 100

                        // Claim phases
                        var claimConditions = await this.getClaimPhases({withAllowList: true})
                        this.claimPhases = this.parseClaimConditions(claimConditions, response.data)

                        // Collection
                        this.collection.totalSupply = await this.contract.totalSupply()
                        this.collection.totalClaimedSupply = await this.contract.totalClaimedSupply()
                        this.collection.totalRatio = Math.round((this.collection.totalClaimedSupply/this.collection.totalSupply)*100)
                        this.collection.nfts = await this.contract.getAll({count: 8})
                    } catch (error) {
                        resportError(error)
                        console.log('error', error)
                        this.setMessage('Contract could not be loaded, please try again.', 'error', true)
                    }

                    this.validateTabStatus()
                })
            },
            setClaimPhasesInfo: function() {
                this.claimPhaseInfo = [
                    "You could use this phase as a whitelist only phase. This allows all whitelisted wallets to mint your NFT. <br/>If you don't want to set a whitelist phase. Then you only need to set this phase.",
                    "You could use this phase as an extra whitelist only phase. This allows all additional whitelisted wallets to mint your NFT. <br/>If you don't want to set another whitelist phase, you can set this phase as the public mint.",
                    "This phase becomes the public mint phase."
                ]
            },
            updateClaimPhases: async function(e) {
                // Validate form
                var validation = this.validateUpdateClaimPhases()
                if (!validation.valid) {
                    this.setMessage(validation.message, 'error')
                    return
                }

                this.setButtonLoader(e)

                var claimPhases = []
                var formData = {}
                for (var i = 0; i < this.claimPhases.length; i++) {
                    var claimPhase = this.claimPhases[i]
                    var newClaimPhase = {
                        metadata: {
                            name: claimPhase.name
                        },
                        startTime: new Date(claimPhase.startTime),
                        price: parseFloat(claimPhase.price),
                        maxClaimableSupply: claimPhase.maxClaimableSupply == 0 ? 'unlimited' : parseInt(claimPhase.maxClaimableSupply),
                        maxClaimablePerWallet: claimPhase.maxClaimablePerWallet == 0 ? 'unlimited' : parseInt(claimPhase.maxClaimablePerWallet),
                        // waitInSeconds: claimPhase.waitInSeconds == 0 ? ethers.constants.MaxUint256 : 5, // Contract v2, Contract v3
                        // snapshot: claimPhase.whitelist == 0 ? [] : claimPhase.snapshot,
                    }
                    claimPhases.push(newClaimPhase)
                }

                try {
                    await this.contract.claimConditions.set(claimPhases)
                    this.validateTabStatus()
                    // await axios.put('/collections/'+this.collectionID+'/claim-phases', formData).then((response) => {
                    //     this.validateTabStatus()
                    // })
                    
                    this.setMessage('Claim phases updated', 'success')
                } catch(error) {
                    resportError(error)
                    this.setMessage('Something went wrong, please try again.', 'error')
                }
                
                this.resetButtonLoader()
            },
            addClaimPhase: function(e) {
                if (this.claimPhases.length >= 3) {
                    this.setMessage('You can only have 3 mint phases', 'error')
                    return
                }
                this.claimPhases.push({
                    startTime: this.formateDatetimeLocal(new Date(Date.now())),
                    price: 0,
                    maxClaimableSupply: 0,
                    maxClaimablePerWallet: 0,
                    whitelist: 0,
                    // waitInSeconds: 1, Contract v2, Contract v3
                    snapshot: [],
                    modal: false,
                    name: 'Phase ' + (this.claimPhases.length + 1)
                })
            },
            deleteClaimPhase: function(index) {
                if (index > -1) {
                    this.claimPhases.splice(index, 1)
                }
            },
            uploadWhitelist: async function(e, index) {
                var files = e.target.files
                var formData = new FormData()
                formData.append('file', files[0])
                await axios.post('/collections/'+this.collectionID+'/whitelist', formData).then((response) => {
                    var data = response.data
                    this.claimPhases[index].snapshot = data
                    // this.toggleWhitelistModal(index, false)
                })
            },
            resetWhitelist: function(index) {
                this.claimPhases[index].snapshot = []
            },
            toggleWhitelistModal: function(index, state) {
                console.log('index', index)
                console.log('state', state)
                this.claimPhases[index].modal = state
            },
            updateMetadata: async function(e) {
                // Validate form
                var validation = this.validateUpdateMetadata()
                if (!validation.valid) {
                    this.setMessage(validation.message, 'error')
                    return
                }

                this.setButtonLoader(e)

                try {
                    await this.contract.metadata.set({
                        name: this.collection.name,
                        description: this.collection.description
                    })

                    var formData = this.collection
                    await axios.put('/collections/'+this.collectionID+'/metadata', formData).then((response) => {
                        this.validateTabStatus()
                    })

                    this.setMessage('General settings updated', 'success')
                } catch(error) {
                    resportError(error)
                    this.setMessage('Something went wrong, please try again.', 'error')
                }

                this.resetButtonLoader()
            },
            updateRoyalties: async function(e) {
                // Validate form
                var validation = this.validateUpdateRoyalties()
                if (!validation.valid) {
                    this.setMessage(validation.message, 'error')
                    return
                }

                this.setButtonLoader(e)

                try {
                    await this.contract.royalties.setDefaultRoyaltyInfo({
                        seller_fee_basis_points: this.collection.royalties * 100, // 1% royalty fee
                        fee_recipient: this.collection.fee_recipient, // the fee recipient
                    })

                    this.validateTabStatus()

                    this.setMessage('Royalties updated', 'success')
                } catch(error) {
                    resportError(error)
                    this.setMessage('Something went wrong, please try again.', 'error')
                }

                this.resetButtonLoader()
            },
            updateMintSettings: async function(e) {
                this.setButtonLoader(e)

                var data = {
                    permalink: this.collection.permalink,
                    title: this.collection.seo.title,
                    description: this.collection.seo.description,
                    image: this.collection.seo.image
                }

                await axios.put('/collections/'+this.collectionID+'/mint', data)
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.setMessage(error.response.data.message, 'error')
                    }
                })
                .then((response) => {
                    if (response) {
                        if (data.image == '') {
                            this.deleteResource('social-sharing')
                        }

                        this.collection.fullMintUrl = this.collection.mintUrl+'/'+this.collection.permalink
                        this.collection.fullEditorUrl = this.collection.editorUrl+'/'+this.collection.permalink

                        this.validateTabStatus()

                        this.setMessage('Mint settings updated', 'success')
                    }
                })

                this.resetButtonLoader()
            },
            deployContract: async function(e) {
                if (this.hasValidChain !== true) {
                    this.setMessage('Please connect to the correct blockchain', 'error')
                    return
                }

                // Validate form
                var validation = this.validateDeployContract()
                if (!validation.valid) {
                    this.setMessage(validation.message, 'error')
                    return
                }

                this.setButtonLoader(e)

                try {
                    // Deploy contract
                    const contractAddress = await this.deployNFTDrop()

                    if (contractAddress) {
                        // Update DB
                        var formData = this.collection
                        formData.address = contractAddress
                        await axios.post('/collections', formData).then((response) => {
                            var data = response.data
                            window.location.href = "/collections/"+data.id+"/edit"
                        })
                    }

                } catch(error) {
                    resportError(error)
                    this.setMessage('Something went wrong, please try again.', 'error')
                }

                this.resetButtonLoader()
            },
            uploadCollection: async function(event) {
                var files = event.target.files
                var metadata = await this.prepareFiles(files)

                if (metadata.status == 'error') {
                    this.setMessage('Invalid collection data', 'error')
                    return;
                }

                this.collection.metadata = metadata.data
                this.collection.previews = this.collection.metadata.slice(0, 8)
                this.upload = false

                this.setMessage('NFTs received', 'success')
            },
            updateCollection: async function(e) {
                this.setButtonLoader(e)

                try {
                    await this.contract.createBatch(this.collection.metadata)
                    this.collection.totalSupply = await this.contract.totalSupply()
                    this.collection.totalClaimedSupply = await this.contract.totalClaimedSupply()
                    this.collection.totalRatio = Math.round((this.collection.totalClaimedSupply/this.collection.totalSupply)*100)
                    this.collection.nfts = await this.contract.getAll({count: 8})
                    this.collection.previews = []
                    
                    if (this.collection.nfts.length > 0) {
                        var data = {url: this.collection.nfts[0].metadata.image}
                        await axios.post('/collections/'+this.collectionID+'/thumb', data).then((response) => {
                            this.validateTabStatus()
                        })
                    }

                    this.setMessage('NFTs added to the collection!', 'success')
                } catch(error) {
                    resportError(error)
                    this.setMessage('Something went wrong, please try again.', 'error')
                }

                this.resetButtonLoader()
            },
            addSocialImage: function(event) {
                var files = event.target.files
                var formData = new FormData()
                formData.append('resource', files[0])
                formData.append('name', 'social-sharing')

                this.uploadResource('social-sharing', formData).then((response) => {
                    this.collection.seo.image = response.data.url
                    this.resources['social-sharing'].loading = false
                }).catch((error) => {
                    if (error.response.data.errors != undefined) {
                        this.setMessage(error.response.data.errors.resource[0], 'error')
                    } else {
                        this.setMessage('Something went wrong, please try again.', 'error')
                    }
                })
            },
            deleteSocialImage: function(event) {
                this.collection.seo.image = ''
            },
            prepareFiles: async function(files) {
                var images = {}
                var json = {}

                for (var i = 0; i < files.length; i++) {
                    var upload = files[i]
                    // const extension = upload.name.slice((upload.name.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase()
                    const filename = upload.name.replace(/\.[^/.]+$/, "")
                    if (upload.type == 'application/json') {
                        json[filename] = upload
                    } else if(this.validFileType(upload)) {
                        upload.id = filename
                        upload.src = URL.createObjectURL(upload)
                        images[filename] = upload
                    }
                }

                var imagesLength = Object.keys(images).length
                var jsonLength = Object.keys(json).length
                if (jsonLength != imagesLength && jsonLength !== 1) {
                    return {
                        status: 'error',
                        message: 'Images and JSON data combination is not correct',
                        data: []
                    }
                }

                const metadata = await this.createMetadata(images, json)
                metadata.sort((a,b) => a.name - b.name);

                return {
                    status: 'success',
                    message: 'Images and JSON data combination',
                    data: metadata
                }
            },
            createMetadata: async function(images, json) {
                var imagesLength = Object.keys(images).length
                var jsonLength = Object.keys(json).length
                var firstImageKey = Object.keys(images)[0]
                var firstJsonKey = Object.keys(json)[0]
                var firstJsonFile = json[firstJsonKey]

                // Parse single JSON file
                if (jsonLength == 1) {
                    var jsonList = {};
                    var jsonData = await this.getJsonData(firstJsonFile)
                    var index = parseInt(firstImageKey)
                    Object.entries(jsonData).forEach((nft) => {
                        jsonList[index] = nft[1]
                        index++
                    })
                // Parse multiple JSON files
                } else {
                    var jsonList = {};
                    for (var i = parseInt(firstJsonKey); i < (jsonLength + parseInt(firstJsonKey)); i++) {
                        jsonList[i] = await this.getJsonData(json[i])
                    }
                }

                // Create metadata array
                var metadata = []
                for (var i = parseInt(firstImageKey); i < (imagesLength + parseInt(firstImageKey)); i++) {
                    var image = images[i]
                    var json = jsonList[image.id]
                    metadata.push({
                        name: json.name,
                        description: json.description != null && json.description != false ? json.description : '',
                        image: image,
                        attributes: json.attributes
                    })
                }

                return metadata
            },
            getJsonData: async (file) => {
                return new Promise((res,rej) => {
                    let reader = new FileReader()
                    reader.onload = function(){
                        res(JSON.parse(reader.result))
                    }
                    reader.readAsText(file)
                })
            },
            validFileType: function(file) {
                switch(file.type) {
                    case 'image/jpeg':
                    case 'image/jpg':
                    case 'image/png':
                    case 'image/gif':
                       return true;
                    default:
                        return false;
                }
            },
            openYouTubeModal: function(url) {
                this.modalToggle(true)
                this.modalTitle('Tutorial video')
                this.modalContent('<div class="w-full text-center"><iframe class="inline-block" width="560" height="315" src="'+url+'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>')
            }
        }
    })
}