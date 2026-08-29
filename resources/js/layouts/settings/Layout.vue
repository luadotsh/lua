<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';

/**
 * The shell the account-level settings screens sit in, shaped after
 * `~/Herd/trypost`. These few screens are reached from the sidebar menu rather
 * than from the nav, so they carry their heading in the content: the app header
 * above them belongs to whatever you were looking at before.
 */
defineProps<{
    title: string;
    description?: string;
}>();
</script>

<template>
    <div class="mx-auto max-w-4xl space-y-8 px-6 py-8">
        <PageHeader :title="title" :description="description" />

        <section class="space-y-12">
            <slot />
        </section>
    </div>
</template>
