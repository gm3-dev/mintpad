window.$ = require('jquery')
import { ethers } from 'ethers'
import helpers from '../includes/helpers.js'
import { initSentry, resportError } from '../includes/sentry'
initSentry(undefined)

export default {
    mixins: [helpers],
    data() {
        return {
            wallet: {
                name: false,
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
            localStorage.setItem('walletName', 'metamask');
            await this.getMetaMaskProvider()
            if (this.wallet.provider) {
                this.setMetaMaskEvents()
                await this.loadMetaMaskAccount(triggerRequest)
            }
        },
        getMetaMaskProvider: async function () {
            try {
                var provider = new ethers.providers.Web3Provider(window.ethereum, "any")
                // this.provider = await detectEthereumProvider() // not used
    
            } catch(error) {
                this.setMessage('MetaMask is not installed - <a href="https://metamask.io/download/" target="_blank" class="underline">download here</a>', 'error', false)
            }
    
            if (provider) {
                // From now on, this should always be true:
                // provider === window.ethereum
                this.wallet.provider = provider
    
                provider.on("pending", function(e) {
                    console.log(e)
                })
            }
        },
        setMetaMaskEvents: function () {
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
        setMetaMaskError: function(error) {
            console.log('error', error)
            console.log('error.code', error.code)
            console.log('error.reason', error.reason)
            switch (error.code) {
                case -32002: 
                    this.setMessage('Request already pending: open MetaMask to see the request.', 'error')
                    return true
                case 4001:
                    this.setMessage('Request canceled: you rejected the request.', 'error')
                    return true
                case 4902:
                    this.setMessage('Unrecognized chain ID: try adding the chain to MetaMask.', 'error')
                    return true
            }

            switch(error.reason) {
                case 'user rejected transaction': 
                    this.setMessage('Request canceled: you rejected the request.', 'error')
                    return true
                case 'Internal JSON-RPC error.':
                    if (error.message.search('execution reverted: !MaxSupply') !== -1) {
                        this.setMessage('There are no more NFTs left to claim in this mint phase.', 'error')
                        return true
                    } else if (error.message.search('execution reverted: !Qty') !== -1) {
                        this.setMessage('You reached the maximum number of claimable NFTs per wallet.', 'error')
                        return true
                    }
            }
            return false
        },
        loadMetaMaskAccount: async function (triggerRequest) {
            var requestAccount = false
            var signer = false
            var account = false
            var chainID = false
            var accounts = []
            var network = false
    
            try {
                signer = this.wallet.provider.getSigner()
                accounts = await ethereum.request({method: 'eth_accounts'})
                if (accounts.length > 0) {
                    account = accounts[0]
                } else {
                    throw Error('Not connected')
                }
                network = await this.wallet.provider.getNetwork()
            } catch (error) {
                if (error.message != 'Not connected') {
                    this.setMessage('Metamask issue. Click <a href="https://mintpad.co/troubleshooting/" target="_blank" class="underline">here</a> to find out more.', 'error', true)
                    resportError(error) 
                }
                requestAccount = true
            }
    
            if (window.ethereum) {
                if (requestAccount && triggerRequest) {
                    try {
                        accounts = await ethereum.request({method: 'eth_requestAccounts'})
                    } catch(error) {
                        this.setMessage('Metamask issue. Click <a href="https://mintpad.co/troubleshooting/" target="_blank" class="underline">here</a> to find out more.', 'error', true)
                        resportError(error) 
                    }
                    if (accounts.length > 0) {
                        account = accounts[0]
                    }
                }
                
                chainID = parseInt(window.ethereum.networkVersion)
                // if (chainID == undefined) {
                //     chainID = parseInt(network.chainId)
                // }
            }
    
            this.wallet.name = 'metamask'
            this.wallet.signer = signer
            this.wallet.account = account
            if (chainID && this.blockchains[chainID]) {
                this.wallet.network = this.blockchains[chainID]
            } else {
                this.wallet.network = {id: chainID}
            }
        }
    }
}