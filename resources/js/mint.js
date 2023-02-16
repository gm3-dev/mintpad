window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'

// Includes
import { initSentry, resportError } from './includes/sentry'
import wallet from './includes/wallet.js'
import metamask from './wallets/metamask.js'
import phantom from './wallets/phantom.js'
import helpers from './includes/helpers.js'
import modal from './includes/modal.js'
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
    Vue.component('dark-mode', require('./components/DarkMode.vue').default);

    new Vue({
        el: '#app',
        mixins: [modal, wallet, metamask, phantom, helpers, thirdweb, thirdwebWrapper],
        data: {
            editMode: false,
            style: {},
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
            timers: {},
            mintAmount: 1,
            loadComplete: false,
            page: {
                name: ''
            },
            activeMintPhase: false,
            settings: {
                phases: true,
                darkmode: false
            }
        },
        async mounted() {
            this.collectionID = false
            if ($('#collectionID').length) {
                this.collectionID = $('#collectionID').val()
            }

            if (!this.collectionID) {
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
                this.collection.chainName = this.blockchains[this.collection.chain_id].full
                this.collection.token = response.data.token
                this.collection.buttons = this.setButtons(response.data.buttons ?? [])
                this.collection.logo = response.data.logo
                this.collection.background = response.data.background
                this.collection.thumb = response.data.thumb
                this.hasValidChain = await this.validateMatchingBlockchains(parseInt(this.collection.chain_id))
                
                // Set theme
                this.setPage()
                if (this.page.name == 'mint.embed') {
                    if (response.data.theme.embed) {
                        this.theme = response.data.theme.embed
                    } else {
                        this.theme = this.theme.embed
                    }
                } else if (this.page.name == 'mint.index') {
                    if (response.data.theme.mint) {
                        this.theme = response.data.theme.mint
                    } else {
                        this.theme = this.theme.mint
                    }
                }
                // Set settings
                if (response.data.settings.embed) {
                    this.settings = response.data.settings.embed

                    if (this.page.name == 'mint.embed') {
                        this.setDarkmode(this.settings.darkmode)
                    }
                }

                this.setStyling()
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
                    this.collection.image = this.collection.thumb ? this.collection.thumb : await this.setCollectionImage()
                    const royalties = await this.contract.royalties.getDefaultRoyaltyInfo()
                    this.collection.royalties = Math.round((royalties.seller_fee_basis_points / 100) * 10) / 10 + '%'

                    // Collection supply
                    this.setSupplyData()
                    setInterval(() => {
                        this.setSupplyData()
                    }, 10000)
                    
                    // Claim phases
                    var claimConditions = await this.getClaimPhases({withAllowList: true})
                    this.claimPhases = this.parseClaimConditions(claimConditions, response.data)
                    this.setClaimPhaseCounters()
                    this.setActiveClaimPhase()
                    
                } catch (error) {
                    resportError(error)
                    this.setMessage('Something went wrong, please try again.', 'error', true)
                }
                this.loadComplete = true

            }).catch((error) => {
                //
            });
        },
        methods: {
            setSupplyData: async function() {
                this.collection.totalSupply = await this.contract.totalSupply()
                this.collection.totalClaimedSupply = await this.contract.totalClaimedSupply()
                this.collection.totalRatio = Math.round((this.collection.totalClaimedSupply/this.collection.totalSupply)*100)
                if (isNaN(this.collection.totalRatio)) {
                    this.collection.totalRatio = 0
                }
            },
            setPage: function() {
                this.page.name = this.$el.getAttribute('data-page')
            },
            previousPhase: function() {
                const phaseCount = this.claimPhases.length-1
                if (this.activeMintPhase == 0) {
                    this.activeMintPhase = phaseCount
                } else {
                    this.activeMintPhase--
                }
            },
            nextPhase: function() {
                const phaseCount = this.claimPhases.length-1
                if (this.activeMintPhase == phaseCount) {
                    this.activeMintPhase = 0
                } else {
                    this.activeMintPhase++
                }
            },
            setActiveClaimPhase: function() {
                for (var i = 0; i < this.claimPhases.length; i++) {
                    var claimPhase = this.claimPhases[i]
                    var from = new Date(claimPhase.startTime).getTime()
                    var to = new Date(claimPhase.endTime).getTime()
                    var now = new Date().getTime()
                    if (now <= to && now >= from) {
                        this.claimPhases[i].active = true
                        this.activeMintPhase = i
                    } else if (now >= from && to == 0) {
                        this.claimPhases[i].active = true
                        this.activeMintPhase = i
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
                for (var i = 0; i < this.claimPhases.length; i++) {
                    this.$set(this.timers, i, {})
                    this.setCountDown(i)
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
            mintNFT: async function(e) {
                if (this.claimPhases.length == 0) {
                    this.setMessage('You cannot mint this NFT yet because no mint phases have been set yet', 'error')
                    return
                }
                this.setButtonLoader(e)

                try {
                    await this.contract.claim(this.mintAmount)

                    if (this.page.name == 'mint.index') {
                        this.modal.id = 'mint-successful'
                    } else {
                        this.setMessage('NFT minted!', 'success')
                    }
                    this.setSupplyData()
                } catch (error) {
                    if (! this.setMetaMaskError(error)) {
                        resportError(error)
                        this.setMessage('Something went wrong, please try again.', 'error', true)
                    }
                }

                this.resetButtonLoader()
            }
        }
    });
}