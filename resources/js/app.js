require('./bootstrap')
window.$ = require('jquery')
import Vue from 'vue/dist/vue.js'
import Alpine from 'alpinejs'
import VueTippy, { TippyComponent } from "vue-tippy";
import { ThirdwebSDK } from '@thirdweb-dev/sdk'
import { BigNumber } from "ethers"
import { initMetaMask } from './MetaMask'
import helpers from './Helpers.js'
import { times } from 'lodash';
// import detectEthereumProvider from '@metamask/detect-provider'
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

if (document.getElementById('app')) {    
    new Vue({
        el: '#app',
        mixins: [helpers],
        data: {
            ipfs: {
                gateway: false,
                hash: 'QmZ7JB3mBYxTD8McJZK8QrVAY7i9JrL3Tqu14GVaYYqnQh',
                embed: ''
            },
            collectionID: false,
            contractAddress: false,
            errorMessage: false,
            successMessage: false,
            message: {
                error: false,
                success: false,
                info: false
            },
            provider: false,
            account: false,
            accounts: false,
            sdk: false,
            wallet: false,
            agreeConditions: false,
            upload: false,
            collection: {
                name: '',
                blockchain: 'ethereum',
                token: 'ETH',
                symbol: '',
                fee_recipient: 0,
                royalties: 0,
                description: '',
                nfts: [],
                previews: [],
                totalSupply: 0,
                totalClaimedSupply: 0,
            },
            claimPhases: [],
            loader: {
                button: {
                    label: '',
                    target: null
                }
            },
            page: {
                name: '',
                tab: 1,
            }
        },
        computed: {
            userAddressShort: function() {
                return this.wallet.account ? this.wallet.account.substring(0, 5)+'...'+this.wallet.account.substr(this.wallet.account.length - 3) : ''
            }
        },
        async mounted() {
            console.log('App mounted')

            if ($('#collectionID').length) {
                this.collectionID = $('#collectionID').val()
            }

            this.setPage()
            this.setPageData()
    
            this.wallet = await initMetaMask(false)
            if (this.wallet.account) {
                $('#user-address > button').text(this.userAddressShort).data('address', this.wallet.account).removeClass('hidden')
            }
        },
        methods: {
            changeEditTab: async function(tab) {
                this.page.tab = tab

                if (this.page.tab == 3) {
                    const contract = await this.getSmartContract()
                    this.collection.totalSupply = await contract.totalSupply()
                    this.collection.totalClaimedSupply = await contract.totalClaimedSupply()
                    this.collection.nfts = await contract.getAll()
                }
            },
            setPage: function() {
                this.page.name = this.$el.getAttribute('data-page')
            },
            setPageData: async function() {
                // Collection edit page
                if (this.page.name == 'collections.edit' || this.page.name == 'collections.claim') {
                    axios.get('/collections/'+this.collectionID+'/fetch').then(async (response) => {

                        this.contractAddress = response.data.address
                        this.collection.blockchain = response.data.blockchain
                        this.collection.token = response.data.token
                        this.setSDK()
                        const contract = await this.getSmartContract()

                        // Create embed code
                        try {
                            this.ipfs.gateway = contract.drop.storage.gatewayUrl
                            const embedUrl = this.buildEmbedUrl()
                            this.ipfs.embed = this.buildEmbedCode(embedUrl)
                        } catch (e) {
                            // console.log('Failed to build embed code', e)
                            // this.setErrorMessage('Could not create embed code')
                        }

                        // Set form data
                        try {
                            const metadata = await contract.metadata.get()
                            const royalties = await contract.royalties.getDefaultRoyaltyInfo()
                            this.collection.name = metadata.name
                            this.collection.description = metadata.description
                            this.collection.fee_recipient = royalties.fee_recipient
                            this.collection.royalties = royalties.seller_fee_basis_points / 100
                        } catch (e) {
                            // console.log('Failed to load metadata', e)
                            this.setErrorMessage('Contract could not be loaded...')
                        }

                        var claimConditions = await contract.claimConditions.getAll()
                        this.claimPhases = this.parseClaimConditions(claimConditions)
                        
                    })
                }
            },
            setSDK: function(e) {
                this.sdk = ThirdwebSDK.fromSigner(this.wallet.signer, this.collection.blockchain, {})
            },
            getSmartContract: async function(e) {
                return await this.sdk.getNFTDrop(this.contractAddress)
            },
            buildEmbedUrl: function() {
                return this.ipfs.gateway+this.ipfs.hash+'/nft-drop.html?contract='+this.contractAddress+'&chainId=80001'
            },
            buildEmbedCode: function(embedUrl) {
                return '<iframe id="embed-iframe"\
                src="'+embedUrl+'"\
                width="600px"\
                height="500px"\
                style="max-width:100%;"\
                frameborder="0"\
                ></iframe>'
            },
            setErrorMessage: function(message) {
                this.errorMessage = message
                setTimeout(() => {
                    this.errorMessage = false
                }, 5000)
            },
            setSuccessMessage: function(message) {
                this.successMessage = message
                setTimeout(() => {
                    this.successMessage = false
                }, 5000)
            },
            connectMetaMask: async function() {
                if (this.wallet.account === false) {
                    this.wallet = await initMetaMask(true)
                }
            },
            setButtonLoader: function(e) {
                var button = $(e.target)
                var buttonWidth = button.outerWidth()
                this.loader.button = {
                    target: button,
                    label: button.text()
                }
                button.css('width', buttonWidth+'px').prop('disabled', true).html('Processing...')
            },
            resetButtonLoader: function(e) {
                var button = this.loader.button.target
                var buttonWidth = button.outerWidth()
                button.css('width', buttonWidth+'px').prop('disabled', false).html(this.loader.button.label)
            },
            updateClaimPhases: async function(e) {
                this.setButtonLoader(e)

                var claimPhases = []
                for (var i = 0; i < this.claimPhases.length; i++) {
                    var claimPhase = this.claimPhases[i]
                    claimPhases.push({
                        startTime: new Date(claimPhase.startTime),
                        price: claimPhase.price,
                        maxQuantity: claimPhase.maxQuantity,
                        quantityLimitPerTransaction: claimPhase.quantityLimitPerTransaction,
                        waitInSeconds: 120,
                        snapshot: claimPhase.whitelist == 0 ? [] : claimPhase.snapshot,
                    })
                }

                try {
                    const contract = await this.getSmartContract()
                    var claimConditions = await contract.claimConditions.set(claimPhases)
                    
                    this.setSuccessMessage('Claim phases updated')
                } catch(error) {
                    console.log('error updateMetadata', error)
                    this.setErrorMessage('error updateMetadata')
                }
                
                this.resetButtonLoader()
            },
            addClaimPhase: function(e) {
                this.claimPhases.push({
                    startTime: this.formateDatetimeLocal(new Date(Date.now())),
                    price: 0,
                    maxQuantity: 0,
                    quantityLimitPerTransaction: 0,
                    whitelist: 0,
                    snapshot: [],
                    modal: false
                })
            },
            deleteClaimPhase: function(index) {
                if (index > -1) {
                    this.claimPhases.splice(index, 1)
                }
            },
            parseClaimConditions: function(claimConditions) {
                var output = []

                for (var i = 0; i < claimConditions.length; i++) {
                    var claimCondition = claimConditions[i]
                    output.push({
                        startTime: this.formateDatetimeLocal(claimCondition.startTime),
                        price: this.hexToValue(claimCondition.price._hex),
                        maxQuantity: parseInt(claimCondition.maxQuantity),
                        quantityLimitPerTransaction: parseInt(claimCondition.quantityLimitPerTransaction),
                        whitelist: claimCondition.snapshot == undefined || claimCondition.snapshot.length == 0 ? 0 : 1,
                        snapshot: claimCondition.snapshot ?? [],
                        modal: false
                    })
                }
                return output
            },
            uploadWhitelist: async function(e, index) {
                var files = e.target.files
                var formData = new FormData()
                formData.append('file', files[0])
                await axios.post('/collections/'+this.collectionID+'/whitelist', formData).then((response) => {
                    var data = response.data
                    this.claimPhases[index].snapshot = data
                    this.toggleWhitelistModal(index, false)
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

                const contract = await this.getSmartContract()
                try {
                    await contract.metadata.set({
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

                const contract = await this.getSmartContract()
                try {
                    await contract.royalties.setDefaultRoyaltyInfo({
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
            deployContract: async function(e) {
                this.setButtonLoader(e)

                // deploy contract
                try {
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

                // const contract = this.getSmartContract()
                // const rolesAndMembers = await contract.roles.getAll()
                // console.log(rolesAndMembers)
                // console.log('this.account', this.wallet.account)
                // await contract.roles.revoke("admin", this.wallet.account)
                // const files = [fs.readFileSync("1.png"), fs.readFileSync("2.png")]
                // const result = await contract.storage.upload(files)
                // console.log('result', result)

                // const royaltyInfo = await contract.royalties.getDefaultRoyaltyInfo()
                // console.log(royaltyInfo)
                // await contract.royalties.setDefaultRoyaltyInfo({
                //     "seller_fee_basis_points": 500
                // })
                // const nfts = await contract.getAllUnclaimed()
                // console.log(nfts)
            },
            uploadCollection: async function(event) {
                var files = event.target.files
                // console.log('files', files)

                this.collection.previews = await this.prepareFiles(files)

                this.setSuccessMessage('Collection deployed')

                // const contract = await this.getSmartContract()
                // try {
                //     // Custom metadata of the NFTs to create
                //     const metadatas = [{
                //         name: "The Boys NFT!",
                //         description: "Awesome show",
                //         image: files[0],
                //         attributes: [{
                //                 "trait_type": "Blood",
                //                 "value": "10"
                //             },
                //             {
                //                 "trait_type": "Chicks",
                //                 "value": "8"
                //             },{
                //                 "trait_type": "Realism",
                //                 "value": "1"
                //         }]
                //     }]
                    
                //     const results = await contract.createBatch(metadatas)
                //     // const results = await contract.getAll({})
                //     // const results = await contract.burn(1)
                //     // const results = await contract.claim(1)
                // } catch(error) {
                //     console.log(error)
                // }


                // FOR BACK USE ONLY
                // const result = await contract.storage.uploadBatch({
                //     files: files,
                //     contractAddress: "0xF2b19FFce4BF4271acE2C3e4c352b2a12e8A9Eb1"
                // })
                // console.log('result', result)

                // this.uploadToPinata(files)

                // const chunks = this.createUploadChunks(Array.from(files), 50)
                // const width = 100 / chunks.length
                // this.upload = {
                //     width: 0
                // }
                // for (var i = 0; i < chunks.length; i++) {
                //     await axios.post('/collections/'+this.collectionID+'/upload', 
                //         this.prepareCollectionForUpload(chunks[i])
                //     ).then((response) => {
                //         this.collection = response.data.images
                //         this.upload = {
                //             width: width * (i+1)
                //         }
                //     })
                // }

                this.upload = false
            },
            prepareFiles: async function(files) {
                var images = {}
                var json = []

                for (var i = 0; i < files.length; i++) {
                    var upload = files[i]
                    // const extension = upload.name.slice((upload.name.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase()
                    const filename = upload.name.replace(/\.[^/.]+$/, "")
                    if (upload.type == 'application/json') {
                        json.push(upload)
                    } else if(this.validFileType(upload)) {
                        upload.id = filename
                        upload.src = URL.createObjectURL(upload)
                        images[filename] = upload
                        // console.log('filename', filename)
                        // console.log('filename', isInteger(filename))
                    }
                }

                if (json.length != images.length && json.length != 1) {
                    return {
                        status: 'error',
                        message: 'Images and JSON data combination is not correct'
                    }
                }
                const metadata = await this.createMetadata(images, json)

                return metadata
            },
            createMetadata: async function(images, json) {
                if (json.length == 1) {
                    var jsonList = await this.getJsonData(json[0])
                } else {
                    var jsonList = [];
                    for (var i = 0; i < json.length; i++) {
                        jsonList.push(await this.getJsonData(json[0]))
                    }
                }
                var metadata = []
                for (var i = 0; i < jsonList.length; i++) {
                    var nft = jsonList[i]
                    metadata.push({
                        name: nft.name,
                        description: nft.description,
                        image: images[nft.name] !== undefined ? images[nft.name] : '',
                        attributes: nft.attributes
                    })
                }

                return metadata
            },
            getJsonData: async (file) => {
                return new Promise((res,rej)=>{
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
            // createUploadChunks: function(files, chunkSize) {
            //     var output = []
            //     for (let i = 0; i < files.length; i += chunkSize) {
            //         const chunk = files.slice(i, i + chunkSize)
            //         output.push(chunk)
            //     }
            //     return output
            // },
            // prepareCollectionForUpload: function(files) {
            //     var formData = new FormData() 
            //     for (var i = 0; i < files.length; i++) {
            //         var file = files[i]
            //         formData.append('files[' + i + ']', file)
            //     }
            //     return formData
            // },
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
            }
        }
    })
}