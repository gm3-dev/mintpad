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
            successMessage: false,
        }
    },
    methods: {
        /**
         * Set error message
         * @param {string} message 
         */
        setErrorMessage: function(message) {
            if (message.code) {
                this.errorMessage = message.message
            } else {
                this.errorMessage = message
            }
            setTimeout(() => {
                this.errorMessage = false
            }, 5000)
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
            var seconds = this.getDoubleDigitNumber(d.getSeconds())
            var date = year + '-' + month + '-' + day
            var time = hours + ':' + minutes + ':' + seconds

            return date + ' ' + time
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