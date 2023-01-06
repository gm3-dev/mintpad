window.$ = require('jquery')
import { ethers } from 'ethers'

// Includes
import { initSentry, resportError } from './sentry'

// Config
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}

export default {
    data() {
        return {
            loader: {
                button: {
                    label: '',
                    target: null
                }
            },
            blockchains: {},
            messageBag: [],
            showModal: false,
            hasValidChain: true,
            theme: {
                primary: 'rgba(0, 119, 255, 1)'
            }
        }
    },
    methods: {
        appReady: function() {
            $('#app, #app-content').removeClass('hidden')
            $('#app-loader').remove()
        },
        setStyling: function() {
            if (this.theme) {
                var css = ''
                css += '.mint-text-primary { color: '+this.theme.primary+' !important; } '
                css += '.mint-bg-primary { background-color: '+this.theme.primary+' !important; } '
                css += '.mint-bg-primary:hover { background-color: '+this.replaceOpacityValue(this.theme.primary, '0.9')+' !important; } '
                css += '.mint-bg-primary-sm { background-color: '+this.replaceOpacityValue(this.theme.primary, '0.25')+' !important; } '
                css += '.mint-bg-primary-md { background-color: '+this.replaceOpacityValue(this.theme.primary, '0.5')+' !important; } '
                css += '.mint-bg-primary-lg { background-color: '+this.replaceOpacityValue(this.theme.primary, '0.75')+' !important; } '
    
                this.styleTag = document.createElement('style')
                this.styleTag.appendChild(document.createTextNode(css))
                document.head.appendChild(this.styleTag)
            }
        },
        replaceOpacityValue(string, opacity) {
            return string.replace(/[^,]+(?=\))/, opacity)
        },
        /**
         * Should be rewritten in 1 method together with wallet copier
         */
        copyContractAddress: function(e) {
            var button = $(e.target)
            var buttonWidth = button.outerWidth()
            var buttonText = button.html()
            button.css('width', buttonWidth+'px').text('Copied')
            setTimeout(function() {
                button.html(buttonText)
            }, 1000)
            navigator.clipboard.writeText(button.data('address'))
        },
        switchBlockchainTo: async function(chainID) {
            var chainID = chainID === false ? this.collection.chain_id : chainID

            try {
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: ethers.utils.hexValue(parseInt(chainID)) }],
                })
            } catch(error) {
                if (! this.setMetaMaskError(error)) {
                    resportError(error)
                    this.setMessage('Failed to switch to the correct blockchain, try to do it manually.', 'error')
                }
            }
        },
        validateMatchingBlockchains: async function(chainID) {
            const blockchain = this.blockchains[chainID]
            if (!blockchain || (blockchain && !blockchain.wallet.includes(this.wallet.name))) {
                return 'wallet'
            } else if (chainID != this.wallet.network.id && this.wallet.name == 'metamask') {
                return 'chain'
            } else {
                return true
            }
        },
        setBlockchains: function() {
            return axios.get('/data/blockchains').then((response) => {
                this.blockchains = response.data
            })
        },
        /**
         * Set error message
         * @param {string} message 
         */
        setMessage: function(message, type, refresh) {
            refresh = refresh == undefined ? false : refresh
            if (message.code) {
                var messageObject = {type: type, message: message.message, refresh: refresh}
            } else {
                var messageObject = {type: type, message: message, refresh: refresh}
            }

            if (this.searchMessageBag(message) === false) {
                this.messageBag.push(messageObject)

                if (!refresh) {
                    setTimeout(() => {
                        var messageIndex = this.searchMessageBag(message)
                        if (this.searchMessageBag(message) !== false) {
                            this.messageBag.splice(messageIndex, 1)
                        }
                    }, 5000, message)
                }
            }
        },
        searchMessageBag: function (message) {
            for (var messageIndex in this.messageBag) {
                if (this.messageBag[messageIndex].message == message) {
                    return messageIndex
                }
            }
            return false
        },
        /**
         * Parses claim conditions
         * @param {array} claimConditions list of claim phases
         * @returns array
         */
        parseClaimConditions: function(claimConditions, data) {
            var output = []

            if (this.collection.chain == 'evm') {
                for (var i = 0; i < claimConditions.length; i++) {
                    var cc = claimConditions[i]
                    var nextIndex = i + 1
                    var nextCc = nextIndex > claimConditions.length ? false : claimConditions[nextIndex]
                    output.push({
                        id: nextIndex,
                        name: cc.metadata != undefined && typeof cc.metadata.name !== 'undefined' ? cc.metadata.name : 'Phase '+nextIndex,
                        startTime: this.formateDatetimeLocal(cc.startTime),
                        endTime: nextCc ? this.formateDatetimeLocal(nextCc.startTime) : false,
                        price: this.hexToValue(cc.price._hex),
                        maxClaimableSupply: cc.maxClaimableSupply == 'unlimited' ? 0 : parseInt(cc.maxClaimableSupply),
                        maxClaimablePerWallet: cc.maxClaimablePerWallet == 'unlimited' ? 0 :  parseInt(cc.maxClaimablePerWallet),
                        // waitInSeconds: parseInt(cc.waitInSeconds) == 5 ? 1 : 0, // Contract v2, Contract v3
                        whitelist: cc.snapshot == undefined || cc.snapshot.length == 0 ? 0 : 1,
                        snapshot: cc.snapshot ?? [],
                        modal: false,
                        countdown: ''
                    })
                }
            }

            return output
        },
        /**
         * Set button loader
         * @param {object} e 
         */
        setButtonLoader: function(e) {
            var button = $(e.target)
            button.prop('disabled', true).addClass('is-loading z-50')
            $('#app-loader-bg').removeClass('hidden')

            // var buttonWidth = button.outerWidth()
            this.loader.button = {
                target: button,
                // label: button.html()
            }
            // button.css('width', buttonWidth+'px').prop('disabled', true).html('Processing...')
        },
        /**
         * Reset button loader
         * @param {object} e 
         */
        resetButtonLoader: function(e) {
            var button = this.loader.button.target
            button.prop('disabled', false).removeClass('is-loading z-50')
            $('#app-loader-bg').addClass('hidden')
            // var buttonWidth = button.outerWidth()
            // button.css('width', buttonWidth+'px').prop('disabled', false).html(this.loader.button.label)
        },
        /**
         * Convert hex to number
         * @param hex 
         * @returns integer
         */
        hexToNumber: function(hex) {
            return parseInt(hex, 16)
        },
        /**
         * Convert Wei to value
         * @param {number} wei 
         * @returns {number}
         */
        WeiToValue: function(wei) {
            return wei / 1000000000000000000
        },
        /**
         * Conert hex to value
         * @param {string} hex 
         * @returns {number}
         */
        hexToValue: function(hex) {
            return this.WeiToValue(parseInt(hex, 16))
        },
        /**
         * Format give datetime value
         * @param {datetime} datetime 
         * @returns {string}
         */
        formateDatetimeLocal: function(datetime) {
            var d = new Date(datetime)
            var year = d.getFullYear()
            var month = this.getDoubleDigitNumber(d.getMonth() + 1)
            var day = this.getDoubleDigitNumber(d.getDate())
            var hours = this.getDoubleDigitNumber(d.getHours())
            var minutes = this.getDoubleDigitNumber(d.getMinutes())
            // var seconds = this.getDoubleDigitNumber(d.getSeconds())
            var date = year + '-' + month + '-' + day
            var time = hours + ':' + minutes

            return date + 'T' + time
        },
        /**
         * Format given number to number with a zero prefixed
         * @param {number} number 
         * @returns {number}
         */
        getDoubleDigitNumber: function(number) {
            return number < 10 ? '0'+number : number
        }
    }
}