<script setup lang="ts">
/** A section heading inside a settings screen. Ported from `~/Herd/trypost`. */
defineProps<{
    title: string;
    description?: string;
}>();
</script>

<template>
    <header>
        <h3 class="mb-0.5 text-base font-bold text-foreground">{{ title }}</h3>
        <p v-if="description" class="text-sm text-foreground/70">
            {{ description }}
        </p>
    </header>
</template>
