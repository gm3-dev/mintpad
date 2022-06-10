require('./bootstrap')
window.$ = require('jquery')
import Vue from 'vue/dist/vue.js'
import Alpine from 'alpinejs'
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // optional for styling
// import { ethers } from "ethers"
// import { ContractFactory } from 'ethers'
import { initMetaMask } from "./MetaMask"
// import detectEthereumProvider from '@metamask/detect-provider'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
// const contractAddress = require('./build/contracts/DazedDucks.json').networks[5777].address
// const contractAbi = require('./build/contracts/DazedDucks.json').abi
// const contractByteCode = require('./build/contracts/DazedDucks.json').bytecode
window.Alpine = Alpine
Alpine.start()

if (document.getElementById('app')) {
    new Vue({
        el: '#app',
        data: {
            collectionID: false,
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
            agreeConditions: false
        },
        computed: {
            userAddressShort: function() {
                return this.wallet.account ? this.wallet.account.substring(0, 5)+'...'+this.wallet.account.substr(this.wallet.account.length - 3) : ''
            }
        },
        async mounted() {
            console.log('App mounted')
            console.log(this.shortAddress)
    
            // if (this.wallet !== false && this.wallet.name == 'metamask') {
                this.wallet = await initMetaMask(false)
                if (this.wallet.account) {
                    $('#user-address').text(this.userAddressShort).data('address', this.wallet.account).removeClass('hidden')
                }
            // }

            // this.collectionID = this.$el.getAttribute('data-id')
        },
        methods: {
            connectMetaMask: async function() {
                if (this.wallet.account === false) {
                    this.wallet = await initMetaMask(true)
                }
            }
        }
    })
}
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