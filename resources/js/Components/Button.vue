<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({
    type: {
        type: String,
        default: "submit",
    },
    href: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <Link v-if="href" :href="href" class="btn">
        <slot />
    </Link>
    <button v-else :type="type" class="btn">
        <slot />
    </button>
</template>
