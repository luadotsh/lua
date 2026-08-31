<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Structured data for the crawlers that read it — and increasingly for the
 * assistants that answer questions about a product without visiting it.
 */
const props = defineProps<{ data: Record<string, unknown> }>();

const json = computed(() =>
    JSON.stringify({ '@context': 'https://schema.org', ...props.data }),
);
</script>

<template>
    <Head>
        <component :is="'script'" type="application/ld+json">{{
            json
        }}</component>
    </Head>
</template>
