<script setup lang="ts">
/**
 * Eyebrow, headline, standfirst. Every section on the site is this shape, and
 * having it in one place is what stops six pages drifting into six slightly
 * different type scales.
 */
withDefaults(
    defineProps<{
        eyebrow: string;
        title: string;
        lead?: string;
        size?: 'page' | 'section';
    }>(),
    { lead: undefined, size: 'page' },
);
</script>

<template>
    <div class="max-w-2xl">
        <p class="label">{{ eyebrow }}</p>
        <component
            :is="size === 'page' ? 'h1' : 'h2'"
            class="mt-3 font-display font-semibold tracking-tight text-balance"
            :class="
                size === 'page'
                    ? 'text-4xl sm:text-5xl'
                    : 'text-3xl sm:text-4xl'
            "
        >
            {{ title }}
        </component>
        <p v-if="lead" class="mt-4 text-lg text-muted-foreground">{{ lead }}</p>
    </div>
</template>
