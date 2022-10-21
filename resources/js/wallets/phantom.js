window.$ = require('jquery')
import * as Sentry from "@sentry/vue";
import * as solanaWeb3 from '@solana/web3.js';
import helpers from '../includes/helpers.js'

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
        connectPhantom: async function() {
            if (this.wallet.account === false) {
                await this.initPhantom(false)
            }
        },
        initPhantom: async function(onlyIfTrusted) {
            localStorage.setItem('walletName', 'phantom');
            const provider = this.getPhantomProvider()
            var account = false
            try {
                const resp = await provider.connect({ onlyIfTrusted: onlyIfTrusted })
                account = resp.publicKey.toString()
                this.setPhantomEvents()
            } catch (error) {
                // { code: 4001, message: 'User rejected the request.' }
            }

            this.wallet.name = 'phantom'
            this.wallet.provider = provider            
            this.wallet.signer = provider
            this.wallet.account = account
            this.wallet.network = {id: 11001}
        },
        getPhantomProvider: function() {
            if ('phantom' in window) {
              const provider = window.phantom?.solana;
          
              if (provider?.isPhantom) {
                return provider;
              }
            }
            return false
        },
        setPhantomEvents: function () {
            const provider = this.getPhantomProvider()
            if (provider) {    
                provider.on('connect', function (info) {
                    console.log('Connected to network', info)
                })
                provider.on('disconnect', function (error) {
                    console.log('Disconnected from network', error)
                })
                provider.on('accountChanged', function (account) {
                    console.log('accountChanged', account)
                    window.location.reload()
                })
            }
        },
        isPhantomInstalled: function() {
            return window.phantom?.solana?.isPhantom
        }
    }
}