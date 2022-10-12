<template>
    <div>
        <p class="text-center mb-4 text-mintpad-500 dark:text-gray-200">We are not connected to your wallet</p>
        <div class="text-center">
            <a href="#" @click.prevent="toggleDropdown" class="inline-block px-4 lg:px-8 py-2 text-sm bg-primary-600 border border-transparent rounded-lg text-white text-center hover:bg-primary-700 active:bg-primary-700 focus:outline-none focus:border-primary-700 disabled:opacity-25 transition ease-in-out duration-150">
                Connect wallet <i class="fas fa-chevron-down ml-2"></i>
            </a>
            <div v-show="state" class="line-block border-2 py-2 rounded-lg bg-white w-1/5 mx-auto text-left mt-2">
                <a href="#" class="inline-block px-3 py-1 w-full hover:text-mintpad-300" @click.prevent="connectMetaMask"><img src="/images/metamask.png" width="20px" class="inline-block mr-1" /> MetaMask</a>
                <a href="#" class="inline-block px-3 py-1 w-full hover:text-mintpad-300" @click.prevent="connectPhantom"><img src="/images/phantom.png" width="20px" class="inline-block mr-1" /> Phantom</a>
            </div>
        </div>
    </div>
</template>

<script>
    import metamask from '../wallets/metamask.js'
    import phantom from '../wallets/phantom.js'

    export default {
        mixins: [metamask,phantom],
        // components: {
        //     'editor': Editor
        // },
        props: {},
        data() {
            return {
                state: false
            }
        },
        methods: {
            toggleDropdown: function() {
                this.state = !this.state
            }
        }
    }
</script>