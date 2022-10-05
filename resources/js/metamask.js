window.$ = require('jquery')
import * as Sentry from "@sentry/vue";
import { ethers } from 'ethers'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}

export default {
    data() {
        return {
            wallet: {
                name: 'metamask',
                account: false,
                network: false,
                provider: false,
                signer: false,
            }
        }
    },
    methods: {
        connectMetaMask: async function() {
            if (this.wallet.account === false) {
                await this.initMetaMask(true)
            }
        },
        initMetaMask: async function(triggerRequest) {
            await this.getProvider()
            if (this.wallet.provider) {
                this.loadWeb3()
                await this.loadAccount(triggerRequest)
            }
        },
        getProvider: async function () {
            try {
                var provider = new ethers.providers.Web3Provider(window.ethereum, "any")
                // var network = await provider.getNetwork()
                // this.provider = await detectEthereumProvider() // not used
    
            } catch(error) {
                this.setErrorMessage('MetaMask is not installed - <a href="https://metamask.io/download/" target="_blank" class="underline">download here</a>', true)
            }
    
            if (provider) {
                // From now on, this should always be true:
                // provider === window.ethereum
                this.wallet.provider = provider
    
                provider.on("pending", function(e) {
                    console.log(e)
                })
            } else {
                this.setErrorMessage('MetaMask is not installed - <a href="https://metamask.io/download/" target="_blank" class="underline">download here</a>', true)
            }
        },
        loadWeb3: function () {
            if (window.ethereum) {
                ethereum.on('accountsChanged', (accounts) => {
                    // Time to reload your interface with accounts[0]!
                    console.log('accountsChanged', accounts)
                    window.location.reload()
                })
    
                ethereum.on('chainChanged', () => {
                    // Time to reload your interface with accounts[0]!
                    console.log('chainChanged')
                    window.location.reload()
                })
    
                ethereum.on('message', function (message) {
                    console.log('message', message)
                })
    
                ethereum.on('connect', function (info) {
                    console.log('Connected to network', info)
                })
    
                ethereum.on('disconnect', function (error) {
                    console.log('Disconnected from network', error)
                    window.location.reload()
                })
            }
        },
        loadAccount: async function (triggerRequest) {
            var requestAccount = false
            var signer = false
            var account = false
            var chainID = false
            var accounts = []
    
            try {
                signer = this.wallet.provider.getSigner()
                accounts = await ethereum.request({method: 'eth_accounts'})
                if (accounts.length > 0) {
                    account = accounts[0]
                } else {
                    throw Error('Not connected')
                }
                // account = await signer.getAddress()
            } catch (error) {
                if (error.message != 'Not connected') {
                    this.setErrorMessage('Metamask issue. Click <a href="https://mintpad.co/troubleshooting/" target="_blank" class="underline">here</a> to find out more.', true)
                    Sentry.captureException(error) 
                }
                requestAccount = true
            }
    
            if (window.ethereum) {
                if (requestAccount && triggerRequest) {
                    try {
                        accounts = await ethereum.request({method: 'eth_requestAccounts'})
                    } catch(error) {
                        this.setErrorMessage('Metamask issue. Click <a href="https://mintpad.co/troubleshooting/" target="_blank" class="underline">here</a> to find out more.', true)
                        Sentry.captureException(error) 
                    }
                    if (accounts.length > 0) {
                        account = accounts[0]
                    }
                }
                
                chainID = window.ethereum.networkVersion
            }
    
            this.wallet.signer = signer
            this.wallet.account = account
            if (chainID && this.blockchains[chainID]) {
                this.wallet.network = this.blockchains[chainID]
            } else {
                this.wallet.network = this.blockchains[1]
            }
        }
    }
}