<script setup lang="ts">
/**
 * The header's title, with the count in brackets.
 *
 * Ported from `~/Herd/imobiliaria`. The count sits in the title rather than on a
 * line of its own because it is the first thing you look for on opening a list —
 * and because it disappears by itself when there is nothing, without leaving
 * "0 results" taking up space on an already empty screen.
 */
defineProps<{
    title: string;
    total?: number | null;
}>();
</script>

<template>
    <h2 class="text-lg font-semibold">
        {{ title }}
        <span v-if="total != null && total > 0" class="text-muted-foreground">({{ total }})</span>
    </h2>
</template>
