<template>
    <div class="fixed left-6 bottom-6" v-bind:class="[!show ? 'w-72' : 'w-1/3']">
        <div class="w-full relative transition-all mb-4 p-4 border border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800 rounded-md">
            <a v-if="show" href="#" class="absolute right-3 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="closeMessage">close</a>
            <a v-else href="#" class="absolute right-3 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="showMessage"><i class="fa-solid fa-arrow-right"></i></a>
            <div v-if="show">
                <h3>Let’s get started!</h3>
                <p>Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step. Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step.</p>
                <p>Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step. Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step.</p>
                <p>Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step. Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step.</p>
                <p>Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step. Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step.</p>
                <p>Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step. Launching a collection can seem complicated. That is why we have made a video where we explain the entire process step by step.</p>
            </div>
            <div v-else>
                <a href="#" class="inline-block mb-0 text-sm w-full" @click.prevent="showMessage">Let’s get started!</a>
            </div>
        </div>
        <a href="#" class="inline-block rounded-full bg-gray-600 text-white w-16 h-16 text-center" @click.prevent="showMessage">
            <i class="fa-solid fa-message-question text-3xl align-middle mt-5"></i>
        </a>
    </div>
</template>

<script>
    export default {
        props: {},
        data() {
            return {
                show: true
            }
        },
        methods: {
            closeMessage() {
                this.show = false
            },
            showMessage() {
                this.show = true
            }
        }
    }
</script>