require('./bootstrap')
window.$ = require('jquery')
import { createApp } from 'vue/dist/vue.esm-bundler.js'
import Alpine from 'alpinejs'
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
    createApp({
        data() {
            return {
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
            }
        },

        async mounted() {
            console.log('App mounted')
    
            // if (this.wallet !== false && this.wallet.name == 'metamask') {
                this.wallet = await initMetaMask(false)
                console.log(this.wallet)
            // }

            this.collectionID = this.$el.getAttribute('data-id')
        },
    
        methods: {
            connectMetaMask: async function() {
                console.log(this.wallet.account)
                if (this.wallet.account === false) {
                    this.wallet = await initMetaMask(true)
                    console.log(this.wallet)
                }
            }
        }
    }).mount('#app')
}