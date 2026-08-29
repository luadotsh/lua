<script setup lang="ts">
import PageHeader from '@/components/PageHeader.vue';
import SettingsNav from '@/components/SettingsNav.vue';

/**
 * The shell every settings screen sits in, shaped after `~/Herd/trypost`.
 *
 * The heading lives here rather than in the app header because settings is one
 * screen with a tab row, not eight screens: the title stays put while the tabs
 * change under it.
 */
withDefaults(
    defineProps<{
        title?: string;
        description?: string;
    }>(),
    {
        title: 'Settings',
        description: 'Manage your account, workspace and billing.',
    },
);
</script>

<template>
    <div class="mx-auto max-w-4xl space-y-8 px-6 py-8">
        <PageHeader :title="title" :description="description" />

        <SettingsNav />

        <section class="space-y-12">
            <slot />
        </section>
    </div>
</template>
