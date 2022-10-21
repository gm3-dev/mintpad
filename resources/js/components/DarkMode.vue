<template>
    <div>
        <a href="#" class="text-lg p-1 mr-2 hover:text-primary-600" @click.prevent="toggleDarkmode">
            <i v-if="dark" class="far fa-sun text-white hover:text-primary-600"></i>
            <i v-else class="far fa-moon"></i>
        </a>
    </div>
</template>

<script>
    import { eventBus } from '../includes/event-bus'

    export default {
        data() {
            return {
                dark: false
            }
        },
        mounted: function() {
            this.dark = useDarkmode
        },
        methods: {
            toggleDarkmode: function(e) {
                var mode = document.documentElement.classList.contains('dark')
                if (mode) {
                    document.documentElement.classList.remove('dark')
                    localStorage.theme = 'light'
                    this.dark = false
                } else {
                    document.documentElement.classList.add('dark')
                    localStorage.theme = 'dark'
                    this.dark = true
                }
            }
        }
    }
</script>