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
            errorMessage: false,
            showRefreshButton: false,
            successMessage: false,
            showModal: false,
            hasValidChain: true,
            theme: {
                primary: 'rgba(0, 113, 249, 1)',
                background: 'rgba(238, 242, 253, 1)',
                title: 'rgba(5, 18, 27, 1)',
                text: 'rgba(101, 111, 119, 1)',
                box: 'rgba(255, 255, 255, 1)',
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
                css += '.main-container { background-color: '+this.theme.background+' !important; } '
                css += '#custom-style-container .text-primary-600 { color: '+this.theme.primary+' !important; } '
                css += '#custom-style-container .text-primary-300 { color: '+this.replaceOpacityValue(this.theme.primary, '0.3')+' !important; } '
                css += '#custom-style-container .bg-primary-600 { background-color: '+this.theme.primary+' !important; } '
                css += '#custom-style-container .bg-primary-300 { background-color: '+this.replaceOpacityValue(this.theme.primary, '0.3')+'; } '
                css += '#custom-style-container .bg-gray-300 { background-color: '+this.replaceOpacityValue(this.theme.primary, '0.3')+'; } '
                css += '#custom-style-container .border-primary-600 { border-color: '+this.theme.primary+' !important; } '
                css += '#custom-style-container .border-primary-300 { border-color: '+this.replaceOpacityValue(this.theme.primary, '0.3')+'; } '
                css += '#custom-style-container .text-mintpad-500 { color: '+this.theme.title+'; } '
                css += '#custom-style-container .text-mintpad-300 { color: '+this.theme.text+'; } '
                css += '#custom-style-container .bg-white { background-color: '+this.theme.box+'; } '
                css += '.tinymce-html h1, .tinymce-html h2, .tinymce-html h3 { color: '+this.theme.title+'; } '
                css += '.tinymce-html a { color: '+this.theme.primary+'; } '
                css += '.tinymce-html p, ul, ol { color: '+this.theme.text+'; } '
    
                this.styleTag = document.createElement('style')
                this.styleTag.appendChild(document.createTextNode(css))
                document.head.appendChild(this.styleTag)
            }
        },
        setBackground: function() {
            if (this.collection.background) {
                // this.style = {background: 'url("'+this.collection.background+'")', backgroundPosition: 'top center'}
                this.style = {background: 'url("'+this.collection.background+'")', backgroundPosition: 'top center', backgroundSize: 'cover'}
            } else {
                this.style = {}
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
            var buttonText = button.text()
            button.css('width', buttonWidth+'px').text('Copied')
            setTimeout(function() {
                button.html('<i class="far fa-copy mr-2"></i>'+buttonText)
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
                resportError(error)
                this.setErrorMessage('Failed to switch to the correct blockchain, try to do it manually.')
            }
        },
        validateMatchingBlockchains: async function(chainID) {
            const blockchain = this.blockchains[chainID]
            if (!blockchain || !blockchain.wallet.includes(this.wallet.name)) {
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
        setErrorMessage: function(message, showRefreshButton) {
            this.showRefreshButton = showRefreshButton == undefined ? false : showRefreshButton
            if (message.code) {
                this.errorMessage = message.message
            } else {
                this.errorMessage = message
            }
            if (!this.showRefreshButton) {
                setTimeout(() => {
                    this.errorMessage = false
                    this.showRefreshButton = false
                }, 5000)
            }
        },
        /**
         * Set success message
         * @param {string} message 
         */
        setSuccessMessage: function(message) {
            this.successMessage = message
            setTimeout(() => {
                this.successMessage = false
            }, 5000)
        },
        /**
         * Parses claim conditions
         * @param {array} claimConditions list of claim phases
         * @returns array
         */
        parseClaimConditions: function(claimConditions, data) {
            var output = []

            console.log(data.phases)

            if (this.collection.chain == 'evm') {
                for (var i = 0; i < claimConditions.length; i++) {
                    var claimCondition = claimConditions[i]
                    var nextIndex = i + 1
                    var nextClaimCondition = nextIndex > claimConditions.length ? false : claimConditions[nextIndex]
                    output.push({
                        id: nextIndex,
                        name: data.phases[nextIndex].name ? data.phases[nextIndex].name : 'Phase '+nextIndex,
                        startTime: this.formateDatetimeLocal(claimCondition.startTime),
                        endTime: nextClaimCondition ? this.formateDatetimeLocal(nextClaimCondition.startTime) : false,
                        price: this.hexToValue(claimCondition.price._hex),
                        maxQuantity: claimCondition.maxQuantity == 'unlimited' ? 0 : parseInt(claimCondition.maxQuantity),
                        waitInSeconds: parseInt(claimCondition.waitInSeconds) == 5 ? 1 : 0,
                        quantityLimitPerTransaction: parseInt(claimCondition.quantityLimitPerTransaction),
                        whitelist: claimCondition.snapshot == undefined || claimCondition.snapshot.length == 0 ? 0 : 1,
                        snapshot: claimCondition.snapshot ?? [],
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
            var buttonWidth = button.outerWidth()
            this.loader.button = {
                target: button,
                label: button.html()
            }
            button.css('width', buttonWidth+'px').prop('disabled', true).html('Processing...')
        },
        /**
         * Reset button loader
         * @param {object} e 
         */
        resetButtonLoader: function(e) {
            var button = this.loader.button.target
            var buttonWidth = button.outerWidth()
            button.css('width', buttonWidth+'px').prop('disabled', false).html(this.loader.button.label)
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