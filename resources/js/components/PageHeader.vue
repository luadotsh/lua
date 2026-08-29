<script setup lang="ts">
/**
 * The title of a screen that carries its heading in the content rather than in
 * the app header — settings, mostly. Ported from `~/Herd/trypost`.
 */
defineProps<{
    title: string;
    description?: string;
    total?: number | null;
}>();
</script>

<template>
    <header class="space-y-1">
        <h1 class="text-2xl font-semibold leading-tight text-foreground sm:text-4xl">
            {{ title }}
            <span v-if="total != null && total > 0" class="text-foreground/40">({{ total }})</span>
        </h1>
        <p v-if="description" class="text-sm text-foreground/70">{{ description }}</p>
    </header>
</template>
