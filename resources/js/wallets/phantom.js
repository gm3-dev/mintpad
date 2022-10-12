window.$ = require('jquery')
import * as Sentry from "@sentry/vue";
import * as solanaWeb3 from '@solana/web3.js';
import helpers from '../helpers.js'

export default {
    mixins: [helpers],
    data() {
        return {
            wallet: {
                name: 'phantom',
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
                await this.initPhantom()
            }
        },
        initPhantom: async function() {
            const provider = this.getPhantomProvider()
            try {
                const resp = await provider.connect()
                console.log(resp.publicKey.toString())
                this.setPhantomEvents()
            } catch (error) {
                // { code: 4001, message: 'User rejected the request.' }
            }
        },
        getPhantomProvider: function() {
            console.log('set phantom provider')
            if ('phantom' in window) {
              const provider = window.phantom?.solana;
          
              if (provider?.isPhantom) {
                return provider;
              }
            }
        },
        setPhantomEvents: function () {
            const provider = this.getPhantomProvider()
            if (provider) {    
                provider.on('connect', function (info) {
                    console.log('Connected to network', info)
                })
    
                provider.on('disconnect', function (error) {
                    console.log('Disconnected from network', error)
                    window.location.reload()
                })
            }
        },
        isPhantomInstalled: function() {
            return window.phantom?.solana?.isPhantom
        }
    }
}