window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'

// Includes
import { initSentry, resportError } from './includes/sentry'
import wallet from './includes/wallet.js'
import metamask from './wallets/metamask.js'
import phantom from './wallets/phantom.js'
import helpers from './includes/helpers.js'
import thirdwebWrapper from './includes/thirdweb-wrapper.js'
import thirdweb from './includes/thirdweb.js'
import { eventBus } from './includes/event-bus'

// Config
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
initSentry(Vue)

if (document.getElementById('app')) {    
    new Vue({
        el: '#app',
        mixins: [wallet,metamask, phantom,helpers,thirdweb, thirdwebWrapper],
        data: {
            style: {},
            tab: 1,
            collection: {
                totalSupply: 0,
                totalClaimedSupply: 0,
                totalRatio: 0,
                image: false,
                logo: false,
                thumbs: false,
                buttons: []
            },
            claimPhases: [],
            timers: {0: {}, 1: {}, 2: {}},
            mintAmount: 1,
            loadComplete: false
        },
        async mounted() {
            this.collectionID = false
            if ($('#collectionID').length) {
                this.collectionID = $('#collectionID').val()
            }

            if (!this.collectionID) {
                this.setFatalError()
                return
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

            axios.get('/'+this.collectionID+'/fetch').then(async (response) => {
                this.contractAddress = response.data.address
                this.collection.chain_id = response.data.chain_id
                this.collection.chain = this.blockchains[this.collection.chain_id].chain
                this.collection.token = response.data.token
                this.collection.buttons = this.setButtons(response.data.buttons ?? [])
                this.collection.about = response.data.about
                this.collection.roadmap = response.data.roadmap
                this.collection.team = response.data.team
                this.collection.logo = response.data.logo
                this.collection.background = response.data.background
                this.collection.thumb = response.data.thumb
                this.hasValidChain = await this.validateMatchingBlockchains(parseInt(this.collection.chain_id))
                
                // Set theme
                this.theme = response.data.theme
                this.setBackground()
                this.setStyling()
                this.setTab()
            
                this.appReady()

                // Set SDK
                if (this.wallet.account && this.hasValidChain === true) {
                    this.setSDKFromSigner(this.wallet.signer, this.collection.chain_id)
                } else {
                    this.setSDK(this.collection.chain_id)
                }

                await this.setSmartContract(this.contractAddress)

                try {
                    // Global settings
                    const metadata = await this.getMetadata()
                    this.collection.name = metadata.name
                    this.collection.description = metadata.description
                    const royalties = await this.contract.royalties.getDefaultRoyaltyInfo()
                    this.collection.fee_recipient = royalties.fee_recipient
                    this.collection.royalties = royalties.seller_fee_basis_points / 100

                    // Collection
                    this.collection.totalSupply = await this.contract.totalSupply()
                    this.collection.totalClaimedSupply = await this.contract.totalClaimedSupply()
                    this.collection.totalRatio = Math.round((this.collection.totalClaimedSupply/this.collection.totalSupply)*100)
                    this.collection.image = this.collection.thumb ? this.collection.thumb : await this.setCollectionImage()
                    if (isNaN(this.collection.totalRatio)) {
                        this.collection.totalRatio = 0
                    }
                    
                    // Claim phases
                    var claimConditions = await this.getClaimPhases({withAllowList: false})
                    this.claimPhases = this.parseClaimConditions(claimConditions, response.data)
                    this.setClaimPhaseCounters()
                    this.setActiveClaimPhase()
                    
                } catch (error) {
                    resportError(error)
                    this.setErrorMessage('Something went wrong, please try again.', true)
                }
                this.loadComplete = true

            }).catch((error) => {
                console.log('error', error)
            });
        },
        methods: {
            setTab: function() {
                if (this.collection.about) {
                    this.tab = 1
                } else if (this.collection.roadmap) {
                    this.tab = 2
                } else if (this.collection.team) {
                    this.tab = 3
                }
            },
            changeTab: function(index) {
                this.tab = index
            },
            setActiveClaimPhase: function() {
                for (var i = 0; i < this.claimPhases.length; i++) {
                    var claimPhase = this.claimPhases[i]
                    var from = new Date(claimPhase.startTime).getTime()
                    var to = new Date(claimPhase.endTime).getTime()
                    var now = new Date().getTime()
                    if (now <= to && now >= from) {
                        this.claimPhases[i].active = true
                    } else if (now >= from && to == 0) {
                        this.claimPhases[i].active = true
                    } else {
                        this.claimPhases[i].active = false
                    }
                }
            },
            setButtons: function(buttons) {
                var output = []
                for (var i = 0; i < buttons.length; i++) {
                    var button = buttons[i]
                    try {
                        new URL(button.href);
                    } catch (error) {
                        button.href = 'https://'+button.href
                    }
                    output.push(button)
                }
                return output
            },
            setCollectionImage: async function() {
                var images = await this.contract.getAll({count: 1})
                if (images.length) {
                    return images[0].metadata.image
                }
                return false
            },
            setClaimPhaseCounters: function() {
                if (this.claimPhases[0]) {
                    this.setCountDown(0)
                }
                if (this.claimPhases[1]) {
                    this.setCountDown(1)
                }
                if (this.claimPhases[2]) {
                    this.setCountDown(2)
                }
            },
            setCountDown: function(i) {
                var claimPhase = this.claimPhases[i]
                var countDownDate = new Date(claimPhase.startTime).getTime()
                var endDate = new Date(claimPhase.endTime).getTime()
                var state = 'Starts'

                var now = new Date().getTime()
                var distance = endDate - now
                if (distance < 0 && endDate != claimPhase.endTime) {
                    this.timers[i] = false
                } else {
                    var x = setInterval(() => {
                        var now = new Date().getTime()
        
                        var distance = countDownDate - now
                        if (distance < 0) {
                            if (endDate) {
                                state = 'Ends'
                            }
                            countDownDate = endDate
                            var distance = countDownDate - now
                        }
    
                        // Last phase with no end date
                        if (endDate === 0 && distance < 0) {
                            clearInterval(x)
                            this.timers[i] = Infinity
                        // Past phases
                        } else if (distance < 0) {
                            clearInterval(x)
                            this.timers[i] = false
                        
                        // Coming or runing phases
                        } else {
                            var days = Math.floor(distance / (1000 * 60 * 60 * 24))
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000)
            
                            this.timers[i] = {
                                state: state,
                                days: this.getDoubleDigitNumber(days),
                                hours: this.getDoubleDigitNumber(hours),
                                minutes: this.getDoubleDigitNumber(minutes),
                                seconds: this.getDoubleDigitNumber(seconds),
                            }
                        }
                    }, 1000)
                }
            },
            createButtonList: function(data) {
                var output = [];
                if (data.website != '' && data.website != null) {
                    output.push({name: 'Visit the website', url: data.website})
                }
                if (data.roadmap != '' && data.roadmap != null) {
                    output.push({name: 'Roadmap', url: data.roadmap})
                }
                if (data.twitter != '' && data.twitter != null) {
                    output.push({name: 'Twitter', url: data.twitter})
                }
                if (data.discord != '' && data.discord != null) {
                    output.push({name: 'Discord', url: data.discord})
                }

                if (output.length == 0) {
                    output = false
                }

                return output;
            },
            mintNFT: async function(e) {
                this.setButtonLoader(e)

                try {
                    await this.contract.claim(this.mintAmount)

                    this.setSuccessMessage('NFT minted!')
                } catch (error) {
                    resportError(error)
                    this.setErrorMessage('Something went wrong, please try again.', true)
                }

                this.resetButtonLoader()
            }
        }
    });
}