<template>
    <div>
        <a href="#" class="text-base p-1.5 mr-4 rounded-md border border-mintpad-200 dark:border-0 bg-white dark:bg-mintpad-800 text-mintpad-700 hover:text-primary-600" @click.prevent="toggleDarkmode">
            <i v-if="dark" class="fa-solid fa-sun-bright text-white hover:text-primary-600"></i>
            <i v-else class="pl-0.5 fa-solid fa-moon"></i>
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