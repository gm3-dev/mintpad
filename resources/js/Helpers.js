export default {
    methods: {
        hexToNumber: function(hex) {
            return parseInt(hex, 16)
        },
        WeiToValue: function(wei) {
            return wei / 1000000000000000000
        },
        hexToValue: function(hex) {
            return this.WeiToValue(parseInt(hex, 16))
        },
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
        getDoubleDigitNumber: function(number) {
            return number < 10 ? '0'+number : number
        }
    }
}