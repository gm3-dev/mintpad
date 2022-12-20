<template>
    <div class="text-center">
        <a ref="connect-wallet" href="#" @click.prevent="toggleDropdown" class="inline-block relative px-3 xl:px-10 py-3 text-sm bg-primary-600 border border-transparent rounded-md text-white text-center hover:bg-primary-700 active:bg-primary-700 focus:outline-none focus:border-primary-700 disabled:opacity-25 transition ease-in-out duration-150">
            Connect your wallet <i class="fas fa-chevron-down ml-2"></i>
        </a>
        <div v-show="open" v-closable="{exclude: ['connect-wallet'], handler: 'hideDropdown'}" class="line-block border py-2 rounded-md bg-white w-1/5 mx-auto text-left mt-2">
            <a href="#" class="inline-block px-3 py-1 w-full hover:text-mintpad-300" @click.prevent="connectMetaMask"><img src="/images/metamask.png" width="20px" class="inline-block mr-1" /> <span class="align-middle dark:text-mintpad-400">MetaMask</span></a>
            <!-- <a href="#" class="inline-block px-3 py-1 w-full hover:text-mintpad-300" @click.prevent="connectPhantom"><img src="/images/phantom.png" width="20px" class="inline-block mr-1" /> <span class="align-middle">Phantom</span></a> -->
        </div>
    </div>
</template>

<script>
    import { eventBus } from '../includes/event-bus'
    // import metamask from '../wallets/metamask.js'
    // import phantom from '../wallets/phantom.js'

    export default {
        // mixins: [metamask,phantom],
        // components: {
        //     'editor': Editor
        // },
        props: {},
        data() {
            return {
                open: false
            }
        },
        methods: {
            toggleDropdown: function() {
                this.open = !this.open
            },
            hideDropdown: function() {
                this.open = false
            },
            connectMetaMask: function() {
                eventBus.$emit('connect-wallet', 'metamask')
            },
            connectPhantom: function() {
                // eventBus.$emit('connect-wallet', 'phantom')
            }
        }
    }
</script>