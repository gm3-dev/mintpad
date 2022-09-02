export default {
    data() {
        return {
            loader: {
                button: {
                    label: '',
                    target: null
                }
            },
            errorMessage: false,
            showRefreshButton: false,
            successMessage: false,
        }
    },
    methods: {
        /**
         * Get chain info
         * @param {string} chain 
         * @returns {object}
         */
        getChainInfo: function(chain) {
            const chains = {
                // Ethereum
                'ethereum': {
                    name: 'Mainnet',
                    id: 1,
                    metamask: 'homestead'
                },
                'rinkeby': { // deprecated
                    name: 'Rinkeby',
                    id: 4,
                    metamask: 'rinkeby'
                },
                'goerli': {
                    name: 'Goerli',
                    id: 5,
                    metamask: 'goerli'
                },
                // Polygon
                'polygon': {
                    name: 'Polygon',
                    id: 137,
                    metamask: 'matic'
                },
                'mumbai': {
                    name: 'Mumbai',
                    id: 80001,
                    metamask: 'maticmum'
                },
                // Fantom
                'fantom': {
                    name: 'Fantom',
                    id: 250,
                    metamask: 'unknown'
                },
                'fantom-testnet': {
                    name: 'Fantom testnet',
                    id: 4002,
                    metamask: 'unknown'
                },
                // Avalanche
                'avalanche': {
                    name: 'Avalanche',
                    id: 43114,
                    metamask: 'unknown'
                },
                'avalanche-testnet': {
                    name: 'Avalanche testnet',
                    id: 43113,
                    metamask: 'unknown'
                },
                // Optimism
                'optimism': {
                    name: 'Optimism',
                    id: 10
                },
                'optimism-testnet': {
                    name: 'Optimism testnet',
                    id: 69
                },
                // Arbitrum
                'arbitrum': {
                    name: 'Arbitrum',
                    id: 42161
                },
                'arbitrum-testnet': {
                    name: 'Arbitrum testnet',
                    id: 421611
                },
            }
            return chains[chain]
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
        parseClaimConditions: function(claimConditions) {
            var output = []
            for (var i = 0; i < claimConditions.length; i++) {
                var claimCondition = claimConditions[i]
                var nextIndex = i + 1
                var nextClaimCondition = nextIndex > claimConditions.length ? false : claimConditions[nextIndex]
                output.push({
                    id: (i+1),
                    startTime: this.formateDatetimeLocal(claimCondition.startTime),
                    endTime: nextClaimCondition ? this.formateDatetimeLocal(nextClaimCondition.startTime) : false,
                    price: this.hexToValue(claimCondition.price._hex),
                    maxQuantity: parseInt(claimCondition.maxQuantity),
                    waitInSeconds: parseInt(claimCondition.waitInSeconds) == 5 ? 1 : 0,
                    quantityLimitPerTransaction: parseInt(claimCondition.quantityLimitPerTransaction),
                    whitelist: claimCondition.snapshot == undefined || claimCondition.snapshot.length == 0 ? 0 : 1,
                    snapshot: claimCondition.snapshot ?? [],
                    modal: false,
                    countdown: ''
                })
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
                label: button.text()
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