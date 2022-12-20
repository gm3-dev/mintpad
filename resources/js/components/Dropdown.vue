<template>
    <div class="relative">
        <button :ref="refname" @click="toggle()" class="hover:cursor-pointer h-8">
            <slot name="button"></slot>
        </button>

        <div v-show="open" v-closable="{exclude: [this.refname], handler: 'hide'}" class="absolute z-50 mt-2 w-48 rounded-md origin-top-right right-0 dark:border dark:border-gray-600">
            <div class="rounded-md ring-1 ring-black ring-opacity-5 bg-white dark:bg-mintpad-700">
                <slot name="links"></slot>  
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['refname'],
        data () {
            return {
                open: false
            }
        },
        mounted() {
            //
        },
        methods: {
            toggle: function() {
                this.open = ! this.open
            },
            hide: function() {
                this.open = false
            }
        }
    }
</script>