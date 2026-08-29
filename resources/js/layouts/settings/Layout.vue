<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';

/**
 * The shell the account-level settings screens sit in, shaped after
 * `~/Herd/trypost`. These few screens are reached from the sidebar menu rather
 * than from the nav, so they carry their heading in the content: the app header
 * above them belongs to whatever you were looking at before.
 */
withDefaults(
    defineProps<{
        title: string;
        description?: string;
        /** Pricing needs room for four columns; forms do not. */
        wide?: boolean;
    }>(),
    { wide: false },
);
</script>

<template>
    <div :class="['mx-auto space-y-8 px-6 py-8', wide ? 'max-w-6xl' : 'max-w-4xl']">
        <PageHeader :title="title" :description="description" />

        <section class="space-y-12">
            <slot />
        </section>
    </div>
</template>
