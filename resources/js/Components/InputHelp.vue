<script setup>
defineProps(["text"]);
</script>

<template>
    <p v-show="text" class="mt-2 text-xs text-zinc-400">
        {{ text }}
    </p>
</template>
