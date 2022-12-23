<template>
    <div>
        <div id="user-address" class="hidden lg:inline-block">
            <button href="#" @click.prevent="copyAddress" class="hidden bg-mintpad-100 dark:bg-mintpad-800 border border-mintpad-200 dark:border-transparent text-mintpad-700 dark:text-gray-200 text-sm rounded-md dark:hover:border hover:border-primary-600 dark:hover:border-primary-600 px-3 py-1 mr-4" content="Copy wallet address" v-tippy></button>
        </div>      
    </div>
</template>

<script>
    import { eventBus } from '../includes/event-bus'

    export default {
        props: {},
        data() {
            return {}
        },
        mounted() {
            //
        },
        methods: {
            copyAddress: function(e) {
                var button = $(e.target)
                var buttonWidth = button.outerWidth()
                var shortAddress = button.text()
                button.css('width', buttonWidth+'px').text('Copied')
                setTimeout(function() {
                    button.text(shortAddress)
                }, 1000)
                navigator.clipboard.writeText(button.data('address'))
            }
        }
    }
</script>