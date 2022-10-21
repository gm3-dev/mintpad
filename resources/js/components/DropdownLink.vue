<template>
    <a :href="href" :target="target" class="block px-4 py-2 text-sm leading-5 text-mintpad-500 hover:text-mintpad-300 transition duration-150 ease-in-out">
        <slot></slot>
    </a>
</template>

<script>
    export default {
        props: ['href','target']
    }
</script>