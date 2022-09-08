window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'
import Alpine from 'alpinejs'
import VueTippy, { TippyComponent } from "vue-tippy"
import metamask from './metamask.js'
import helpers from './helpers.js'
import modal from './modal.js'
import { ethers } from 'ethers'
import thirdweb from './thirdweb.js'
import nftgenerator from './nft-generator.js'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
window.Alpine = Alpine
Alpine.start()
Vue.use(VueTippy)
Vue.component("tippy", TippyComponent)

// User address in navigation
if (document.getElementById('user-address')) {  
    new Vue({
        el: '#user-address',
        methods: {
            copyAddress: function(e) {
                var button = $(e.target)
                var buttonWidth = button.outerWidth()
                var shortAddress = button.text()
                button.css('width', buttonWidth+'px').text('Copied')
                setTimeout(function() {
                    button.text(shortAddress)
                }, 1000)
                navigator.clipboard.writeText(button.data('address'))
            }
        }
    })
}

if (document.getElementById('app')) {    
    new Vue({
        el: '#app',
        mixins: [metamask,helpers,modal,thirdweb,nftgenerator],
        data: {
            collectionID: false,
            sdk: false,
            contract: false,
            contractAddress: false,
            upload: false,
            collection: {
                name: '',
                chain_id: 1,
                token: 'ETH',
                symbol: '',
                fee_recipient: 0,
                royalties: 0,
                description: '',
                metadata: [],
                nfts: [],
                previews: [],
                totalSupply: 0,
                totalClaimedSupply: 0,
                permalink: '',
                website: '',
                roadmap: '',
                twitter: '',
                discord: '',
                about: '',
            },
            claimPhases: [],
            claimPhaseInfo: [],
            page: {
                name: '',
                tab: 0,
            }
        },
        computed: {
            userAddressShort: function() {
                return this.wallet.account ? this.wallet.account.substring(0, 5)+'...'+this.wallet.account.substr(this.wallet.account.length - 3) : ''
            },
            collectionChain() {
                return this.collection.chain_id
            }
        },
        watch: {
            collectionChain: async function(chainID) {
                this.hasValidChain = await this.validateMatchingBlockchains(parseInt(chainID))
            }
        },
        async mounted() {
            if ($('#collectionID').length) {
                this.collectionID = $('#collectionID').val()
            }
    
            await this.setBlockchains()
            await this.initMetaMask(false)
            this.appReady()
            
            if (this.wallet.account) {
                $('#user-address > button').text(this.userAddressShort).data('address', this.wallet.account).removeClass('hidden')
            }

            this.setPage()
            this.setPageData()
        },
        methods: {
            appReady: function() {
                $('#app').removeClass('hidden')
                $('#app-loader').remove()
            },
            changeEditTab: async function(tab) {
                this.page.tab = tab
            },
            setPage: function() {
                this.page.name = this.$el.getAttribute('data-page')
            },
            setPageData: async function() {

                // Collection pages
                if (this.page.name == 'collections.create') {
                    this.collection.chain_id = this.wallet.network.id
                    this.hasValidChain = await this.validateMatchingBlockchains(this.collection.chain_id)

                } else if (this.page.name == 'collections.edit' || this.page.name == 'collections.claim') {
                    this.setClaimPhasesInfo()

                    axios.get('/collections/'+this.collectionID+'/fetch').then(async (response) => {
                        // Set DB data
                        this.contractAddress = response.data.address
                        this.collection.chain_id = response.data.chain_id
                        this.collection.token = response.data.token

                        // Mint settings
                        this.collection.permalink = response.data.permalink
                        this.collection.website = response.data.website
                        this.collection.roadmap = response.data.roadmap
                        this.collection.twitter = response.data.twitter
                        this.collection.discord = response.data.discord
                        this.collection.about = response.data.about

                        // Check if wallet is connected to the correct blockchain
                        if (!await this.validateMatchingBlockchains(response.data.chain_id)) {
                            this.page.tab = -1
                            return;
                        } else {
                            this.page.tab = 1
                        }

                        this.setSDKFromSigner(this.wallet.signer, this.collection.chain_id)
                        await this.setSmartContract(this.contractAddress)

                        // Global settings
                        try {
                            const metadata = await this.contract.metadata.get()
                            const royalties = await this.contract.royalties.getDefaultRoyaltyInfo()
                            this.collection.name = metadata.name
                            this.collection.description = metadata.description
                            this.collection.fee_recipient = royalties.fee_recipient
                            this.collection.royalties = royalties.seller_fee_basis_points / 100
                        } catch (error) {
                            this.setErrorMessage('Contract could not be loaded...', true)
                        }

                        // Claim phases
                        try {
                            var claimConditions = await this.contract.claimConditions.getAll()
                            this.claimPhases = this.parseClaimConditions(claimConditions)
                        } catch (error) {
                            // console.log('Failed to load claim conditions', error)
                            // this.setErrorMessage('Claim phases could not be loaded...')
                        }

                        // Collection
                        try {
                            this.collection.totalSupply = await this.contract.totalSupply()
                            this.collection.totalClaimedSupply = await this.contract.totalClaimedSupply()
                            this.collection.totalRatio = Math.round((this.collection.totalClaimedSupply/this.collection.totalSupply)*100)
                            this.collection.nfts = await this.contract.getAll({count: 8})
                        } catch(error) {
                            // this.setErrorMessage('Claim phases could not be loaded...')
                        }
                    })
                }
            },
            connectMetaMask: async function() {
                if (this.wallet.account === false) {
                    await this.initMetaMask(true)
                }
            },
            setClaimPhasesInfo: function() {
                this.claimPhaseInfo = [
                    "You could use this phase as a whitelist only phase. This allows all whitelisted wallets to mint your NFT. <br/>If you don't want to set a whitelist phase. Then you only need to set this phase.",
                    "You could use this phase as an extra whitelist only phase. This allows all additional whitelisted wallets to mint your NFT. <br/>If you don't want to set another whitelist phase, you can set this phase as the public mint.",
                    "This phase becomes the public mint phase."
                ]
            },
            updateClaimPhases: async function(e) {
                this.setButtonLoader(e)

                var claimPhases = []
                for (var i = 0; i < this.claimPhases.length; i++) {
                    var claimPhase = this.claimPhases[i]
                    var newClaimPhase = {
                        startTime: new Date(claimPhase.startTime),
                        price: claimPhase.price,
                        maxQuantity: claimPhase.maxQuantity,
                        quantityLimitPerTransaction: 1,
                        waitInSeconds: claimPhase.waitInSeconds == 0 ? ethers.constants.MaxUint256 : 5,
                        snapshot: claimPhase.whitelist == 0 ? [] : claimPhase.snapshot,
                    }
                    claimPhases.push(newClaimPhase)
                }

                try {
                    await this.contract.claimConditions.set(claimPhases)
                    
                    this.setSuccessMessage('Claim phases updated')
                } catch(error) {
                    // console.log('error updateMetadata', error)
                    this.setErrorMessage('error updateMetadata')
                }
                
                this.resetButtonLoader()
            },
            addClaimPhase: function(e) {
                if (this.claimPhases.length >= 3) {
                    this.setErrorMessage('You can only have 3 mint phases')
                    return
                }
                this.claimPhases.push({
                    startTime: this.formateDatetimeLocal(new Date(Date.now())),
                    price: 0,
                    maxQuantity: 0,
                    // quantityLimitPerTransaction: 0,
                    whitelist: 0,
                    waitInSeconds: 1,
                    snapshot: [],
                    modal: false
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
                this.claimPhases[index].modal = state
            },
            updateMetadata: async function(e) {
                this.setButtonLoader(e)

                try {
                    await this.contract.metadata.set({
                        name: this.collection.name,
                        description: this.collection.description
                    })

                    this.setSuccessMessage('General settings updated')

                } catch(error) {
                    // console.log('error updateMetadata', error)
                    this.setErrorMessage('General settings not updated')
                }


                this.resetButtonLoader()
            },
            updateRoyalties: async function(e) {
                this.setButtonLoader(e)

                try {
                    await this.contract.royalties.setDefaultRoyaltyInfo({
                        seller_fee_basis_points: this.collection.royalties * 100, // 1% royalty fee
                        fee_recipient: this.collection.fee_recipient, // the fee recipient
                    })

                    this.setSuccessMessage('Royalties updated')
                } catch(error) {
                    // console.log('error updateRoyalties', error)
                    this.setErrorMessage('Royalties not updated')
                }

                this.resetButtonLoader()
            },
            updateMintSettings: async function(e) {
                this.setButtonLoader(e)

                var data = {
                    permalink: this.collection.permalink,
                    website: this.collection.website,
                    roadmap: this.collection.roadmap,
                    twitter: this.collection.twitter,
                    discord: this.collection.discord,
                    about: this.collection.about,
                }

                await axios.put('/collections/'+this.collectionID, data)
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.setErrorMessage(error.response.data.message)
                    }
                })
                .then((response) => {
                    if (response) {
                        this.setSuccessMessage('Mint settings updated')
                    }
                })

                this.resetButtonLoader()
            },
            deployContract: async function(e) {
                if (!this.hasValidChain) {
                    this.setErrorMessage('Please connect to the correct blockchain')
                    return
                }

                this.setButtonLoader(e)

                // deploy contract
                try {
                    this.setSDKFromSigner(this.wallet.signer, this.collection.chain_id)
                    const contractAddress = await this.sdk.deployer.deployNFTDrop({
                        name: this.collection.name,
                        symbol: this.collection.symbol,
                        description: this.collection.description,
                        primary_sale_recipient: this.wallet.account, // primary sales
                        fee_recipient: this.wallet.account, // royalties address
                        seller_fee_basis_points: this.collection.royalties * 100, // royalties address
                        platform_fee_recipient: '0x892a99573583c6490526739bA38BaeFae10a84D4', // platform fee address
                        platform_fee_basis_points: 250 // platform fee (2,5%)
                    })

                    var formData = this.collection
                    formData.address = contractAddress
                    await axios.post('/collections', formData).then((response) => {
                        window.location.href = "/collections"
                    })

                } catch(error) {
                    // console.log('error deploying contract', error)
                    this.setErrorMessage('Smart contract deployment failed')
                }

                this.resetButtonLoader()
            },
            uploadCollection: async function(event) {
                var files = event.target.files
                var metadata = await this.prepareFiles(files)

                if (metadata.status == 'error') {
                    this.setErrorMessage('Invalid collection data')
                    return;
                }

                this.collection.metadata = metadata.data
                this.collection.previews = this.collection.metadata.slice(0, 8)
                this.upload = false

                this.setSuccessMessage('NFTs uploaded')
            },
            updateCollection: async function(e) {
                this.setButtonLoader(e)

                try {
                    await this.contract.createBatch(this.collection.metadata)

                    this.setSuccessMessage('NFTs added to the collection!')
                } catch(error) {
                    // console.log('error updateCollection', error)
                    this.setErrorMessage('Error while uploading your collection')
                }

                this.resetButtonLoader()
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
            /**
             * Should be rewritten in 1 method together with wallet copier
             */
            copyContractAddress: function(e) {
                var button = $(e.target)
                var buttonWidth = button.outerWidth()
                var buttonText = button.text()
                button.css('width', buttonWidth+'px').text('Copied')
                setTimeout(function() {
                    button.html('<i class="far fa-copy mr-2"></i>'+buttonText)
                }, 1000)
                navigator.clipboard.writeText(button.data('address'))
            },
            openYouTubeModal: function(url) {
                this.modalToggle(true)
                this.modalContent('<div class="w-full text-center"><iframe class="inline-block" width="650" height="366" src="'+url+'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>')
            }
        }
    })
}