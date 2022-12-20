<template>
    <div>
        <a href="#" class="text-lg p-1 mr-4 rounded-md border border-mintpad-200 text-mintpad-700 hover:text-primary-600" @click.prevent="toggleDarkmode">
            <i v-if="dark" class="fa fa-sun text-white hover:text-primary-600"></i>
            <i v-else class="pl-1 pt-1 pb-1 fa-solid fa-moon"></i>
        </a>
    </div>
</template>

<script>
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