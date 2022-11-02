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
        return {}
    },
    methods: {
        disconnectWallet: function() {
            localStorage.removeItem('walletName')
            window.location.reload()
        },
        connectWallet: async function(wallet) {
            if (wallet == 'metamask') {
                await this.connectMetaMask()
            }
            if (wallet == 'phantom') {
                await this.connectPhantom()
            }
            this.setWalletUI()
            this.setPage()
            this.setPageData()
        },
        initWallet: async function(wallet) {
            if (wallet == 'metamask') {
                await this.initMetaMask(false)
            }
            if (wallet == 'phantom') {
                await this.initPhantom(true)
            }
            this.setWalletUI()
        },
        setWalletUI: function() {
            if (this.wallet.account) {
                $('#user-address > button').text(this.userAddressShort).data('address', this.wallet.account).removeClass('hidden')
            }
        },
    }
}