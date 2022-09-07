window.$ = require('jquery')
import Vue from 'vue/dist/vue.min.js'
import metamask from './metamask.js'
import helpers from './helpers.js'
import thirdweb from './thirdweb.js'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}

if (document.getElementById('app')) {    
    new Vue({
        el: '#app',
        mixins: [metamask,helpers,thirdweb],
        data: {
            collection: {
                totalSupply: 0,
                totalClaimedSupply: 0,
                totalRatio: 0,
            },
            claimPhases: [],
            timers: {0: {}, 1: {}, 2: {}},
            mintAmount: 1
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
            await this.initMetaMask(false)

            axios.get('/mint/'+this.collectionID+'/fetch').then(async (response) => {
                this.contractAddress = response.data.address
                this.collection.chain_id = response.data.chain_id
                this.collection.token = response.data.token
                this.collection.buttons = this.createButtonList(response.data)
                this.collection.about = response.data.about
                this.hasValidChain = await this.validateMatchingBlockchains(parseInt(this.collection.chain_id))

                // Set SDK
                if (this.wallet.account && this.hasValidChain) {
                    this.setSDKFromSigner(this.wallet.signer, this.collection.chain_id)
                } else {
                    this.setSDK(this.collection.chain_id)
                }

                await this.setSmartContract(this.contractAddress)

                try {
                    const metadata = await this.contract.metadata.get()
                    const royalties = await this.contract.royalties.getDefaultRoyaltyInfo()
                    this.collection.name = metadata.name
                    this.collection.description = metadata.description
                    this.collection.fee_recipient = royalties.fee_recipient
                    this.collection.royalties = royalties.seller_fee_basis_points / 100
                    this.collection.totalSupply = await this.contract.totalSupply()
                    this.collection.totalClaimedSupply = await this.contract.totalClaimedSupply()
                    this.collection.totalRatio = Math.round((this.collection.totalClaimedSupply/this.collection.totalSupply)*100)
                    this.collection.image = await this.setCollectionImage()
                    if (isNaN(this.collection.totalRatio)) {
                        this.collection.totalRatio = 0
                    }
                } catch (e) {
                    console.log('Failed to load metadata', e)
                    this.setErrorMessage('Contract could not be loaded...', true)
                }

                try {
                    var claimConditions = await this.contract.claimConditions.getAll()
                    this.claimPhases = this.parseClaimConditions(claimConditions)
                    this.setClaimPhaseCounters()
                    this.setActiveClaimPhase()
                } catch (e) {
                    // console.log('Failed to load metadata', e)
                    // this.setErrorMessage('Claim phases could not be loaded...')
                }

            }).catch((error, asdf) => {
                //
            });
        },
        methods: {
            connectMetaMask: async function() {
                if (this.wallet.account === false) {
                    this.wallet = await this.initMetaMask(true)
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
                    } else if (now >= from && to == 0) {
                        this.claimPhases[i].active = true
                    } else {
                        this.claimPhases[i].active = false
                    }
                }
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
                    // console.log(error)
                    this.setErrorMessage(error)
                }

                this.resetButtonLoader()
            }
        }
    });
}