require('./bootstrap')
window.$ = require('jquery')
import Vue from 'vue/dist/vue.js'
import Alpine from 'alpinejs'
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import { ThirdwebSDK } from "@thirdweb-dev/sdk"
// import { ethers } from "ethers"
import { initMetaMask } from "./MetaMask"
// import detectEthereumProvider from '@metamask/detect-provider'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
window.Alpine = Alpine
Alpine.start()

if (document.getElementById('app')) {

    // Vue.component('blockchain-selector', require('./components/BlockchainSelector.vue').default);
    new Vue({
        el: '#app',
        data: {
            ipfs: {
                gateway: false,
                hash: 'QmZ7JB3mBYxTD8McJZK8QrVAY7i9JrL3Tqu14GVaYYqnQh',
                embed: ''
            },
            collectionID: false,
            contractAddress: false,
            errorMessage: false,
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
            collection: [],
            forms: {
                collection: {
                    name: '',
                    blockchain: 'ethereum',
                    symbol: '',
                    fee_recipient: 0,
                    royalties: 0,
                    description: ''
                }
            },
            loader: {
                button: {
                    label: '',
                    target: null
                }
            },
            page: {
                name: ''
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
                $('#user-address').text(this.userAddressShort).data('address', this.wallet.account).removeClass('hidden')
            }
        },
        methods: {
            setPage: function() {
                this.page.name = this.$el.getAttribute('data-page')
                console.log(this.page.name)
            },
            setPageData: function() {
                // Collection edit page
                if (this.page.name == 'collections.edit') {
                    axios.get('/collections/'+this.collectionID+'/fetch').then(async (response) => {

                        this.contractAddress = response.data.address
                        this.sdk = await ThirdwebSDK.fromSigner(this.wallet.signer, 'polygon', {})
                        const contract = await this.sdk.getNFTDrop(this.contractAddress)

                        console.log(contract)

                        // Create embed code
                        try {
                            this.ipfs.gateway = contract.drop.storage.gatewayUrl
                            const embedUrl = this.buildEmbedUrl()
                            console.log('embedUrl', embedUrl)
                            this.ipfs.embed = this.buildEmbedCode(embedUrl)
                        } catch (e) {
                            console.log('Failed to build embed code', e)
                            this.setErrorMessage('Could not create embed code')
                        }

                        // Set form data
                        try {
                            const metadata = await contract.metadata.get()
                            const royalties = await contract.royalties.getDefaultRoyaltyInfo()
                            this.forms.collection.name = metadata.name
                            this.forms.collection.description = metadata.description
                            this.forms.collection.fee_recipient = royalties.fee_recipient
                            this.forms.collection.royalties = royalties.seller_fee_basis_points / 100
                        } catch (e) {
                            console.log('Failed to load metadata', e)
                            this.setErrorMessage('Contract could not be loaded...')
                        }
                    })
                }
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
            updateMetadata: async function(e) {
                this.setButtonLoader(e)

                this.sdk = ThirdwebSDK.fromSigner(this.wallet.signer, this.forms.collection.blockchain, {})
                const contract = this.sdk.getNFTDrop(this.contractAddress)

                try {
                    await contract.metadata.set({
                        name: this.forms.collection.name,
                        description: this.forms.collection.description
                    });

                } catch(error) {
                    console.log('error updateMetadata', error)
                    this.setErrorMessage('error updateMetadata')
                }

                this.resetButtonLoader()
            },
            updateRoyalties: async function(e) {
                this.setButtonLoader(e)

                this.sdk = ThirdwebSDK.fromSigner(this.wallet.signer, this.forms.collection.blockchain, {})
                const contract = this.sdk.getNFTDrop(this.contractAddress)

                try {
                    await contract.royalties.setDefaultRoyaltyInfo({
                        seller_fee_basis_points: this.forms.collection.royalties * 100, // 1% royalty fee
                        fee_recipient: this.forms.collection.fee_recipient, // the fee recipient
                    });

                } catch(error) {
                    console.log('error updateRoyalties', error)
                    this.setErrorMessage('error updateRoyalties')
                }

                this.resetButtonLoader()
            },
            deployContract: async function(e) {
                this.setButtonLoader(e)

                this.sdk = ThirdwebSDK.fromSigner(this.wallet.signer, this.forms.collection.blockchain, {})

                // deploy contract
                try {
                    const contractAddress = await this.sdk.deployer.deployNFTDrop({
                        name: this.forms.collection.name,
                        symbol: this.forms.collection.symbol,
                        description: this.forms.collection.description,
                        primary_sale_recipient: this.wallet.account, // primary sales
                        fee_recipient: this.wallet.account, // royalties address
                        seller_fee_basis_points: this.forms.collection.royalties * 100, // royalties address
                        platform_fee_recipient: '0x892a99573583c6490526739bA38BaeFae10a84D4', // platform fee address
                        platform_fee_basis_points: 250 // platform fee (2,5%)
                    })

                    var formData = this.forms.collection
                    formData.address = contractAddress
                    await axios.post('/collections', formData).then((response) => {
                        console.log(response)
                    })

                } catch(error) {
                    console.log('error deploying contract', error)
                }

                // const contract = this.sdk.getNFTDrop("0xdeAfA4be5b0Ca4cC2154A0B26C236CE0F9d1303F")
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
                // console.log(nfts);

                window.location.href = "/collections";
            },
            validateUpload: function(uploads) {
                var images = []
                var json = []

                for (var i = 0; i < uploads.length; i++) {
                    var upload = uploads[i]
                    // const extension = upload.name.slice((upload.name.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase()
                    const filename = upload.name.replace(/\.[^/.]+$/, "")
                    if (upload.type == 'application/json') {
                        json.push(upload)
                    } else {
                        images.push(upload)
                        console.log('filename', filename)
                        console.log('filename', isInteger(filename))
                    }
                }
            },
            uploadCollection: async function(event) {
                console.log('Uploaded collection')
                // var files = event.target.files
                // console.log('files', files)
                // console.log('files', Array.from(files))

                // this.validateUpload(files)

                // const metadatas = [
                //     {
                //       name: "Cool NFT",
                //       description: "This is a cool NFT",
                //       image: files[0], // This can be an image url or file
                //     //   properties: files[0], // This can be an image url or file
                //     }
                // ];

                this.sdk = ThirdwebSDK.fromSigner(this.wallet.signer, 'polygon', {})
                const contract = this.sdk.getNFTDrop("0xdeAfA4be5b0Ca4cC2154A0B26C236CE0F9d1303F")
                try {
                    // const results = await contract.getAll({})
                    // const results = await contract.burn(1)
                    // const results = await contract.claim(1)
                    // console.log('results', results)
                } catch(error) {
                    console.log(error)
                }

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
            createUploadChunks: function(files, chunkSize) {
                var output = []
                for (let i = 0; i < files.length; i += chunkSize) {
                    const chunk = files.slice(i, i + chunkSize)
                    output.push(chunk)
                }
                return output
            },
            prepareCollectionForUpload: function(files) {
                var formData = new FormData() 
                for (var i = 0; i < files.length; i++) {
                    var file = files[i]
                    formData.append('files[' + i + ']', file)
                }
                return formData
            },
            copyContractAddress: function(e) {
                console.log(e)
                var button = $(e.target)
                var buttonWidth = button.outerWidth()
                console.log(buttonWidth);
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
tippy('[data-tippy-content]');
tippy('.transaction-button', {
    content: 'This action will trigger a transaction',
    placement: 'top',
});
tippy('#user-address', {
    content: 'Copy address',
    placement: 'left',
});
$('.main-container').on('click', '#user-address', function(e) {
    e.preventDefault()
    var button = $(this)
    var buttonWidth = button.outerWidth()
    var shortAddress = button.text()
    button.css('width', buttonWidth+'px').text('Copied')
    setTimeout(function() {
        button.text(shortAddress)
    }, 1000)
    navigator.clipboard.writeText(button.data('address'))
})