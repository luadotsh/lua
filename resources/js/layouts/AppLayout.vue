<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
    fullWidth?: boolean;
    title?: string;
    total?: number | null;
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    fullWidth: false,
    title: '',
    total: null,
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :full-width="fullWidth" :title="title" :total="total">
        <template v-if="$slots.header" #header>
            <slot name="header" />
        </template>
        <template v-if="$slots['header-actions']" #header-actions>
            <slot name="header-actions" />
        </template>
        <slot />
    </AppLayout>
</template>
