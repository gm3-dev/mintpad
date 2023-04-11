<script setup>
import { Link } from '@inertiajs/vue3'

</script>
<template>
    <Link :href="route('collections.index')" class="header-logo relative text-2xl font-jpegdev text-mintpad-700">
        <img src="/images/logo.svg" class="h-[35px] darkmode-off" />
        <img src="/images/logo-white.svg" class="h-[35px] darkmode-on" />
    </Link>
</template>