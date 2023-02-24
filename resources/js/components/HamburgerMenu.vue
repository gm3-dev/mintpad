<script setup>
import { ref } from 'vue';

let open = ref(false)

function toggle() {
    open.value = !open.value
}
</script>
<template>
    <div>
        <!-- Hamburger -->
        <div class="absolute right-6 top-6 flex items-center sm:hidden">
            <button @click="toggle()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-mintpad-300 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-mintpad-300 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path v-bind:class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-bind:class="{'hidden': ! open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Responsive Navigation Menu -->
        <div v-bind:class="{'block': open, 'hidden': ! open}" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <slot name="main"></slot>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <slot name="settings"></slot>
            </div>
        </div>
    </div>
</template>