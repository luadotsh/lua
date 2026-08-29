<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import HeaderTitle from '@/components/HeaderTitle.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

/**
 * The app shell's header bar, ported from `~/Herd/imobiliaria`.
 *
 * The left side is the screen's identity, the right side its actions. Most
 * screens only need `title` — passing it is what keeps every header the same
 * without each page rebuilding the bar. The `header` slot is the escape hatch
 * for the few that need something other than a title there.
 */
withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
        title?: string;
        total?: number | null;
    }>(),
    {
        breadcrumbs: () => [],
        title: '',
        total: null,
    },
);
</script>

<template>
    <header
        class="flex h-14 shrink-0 items-center justify-between gap-2 border-b border-border bg-card px-4"
    >
        <div class="flex min-w-0 items-center gap-2">
            <!--
                Hidden on desktop: there the sidebar no longer collapses from the
                header. Below `md` it is the only way to open the drawer.
            -->
            <SidebarTrigger class="-ml-1 md:hidden" />
            <slot name="header">
                <HeaderTitle v-if="title" :title="title" :total="total" />
                <Breadcrumbs v-else-if="breadcrumbs.length > 0" :breadcrumbs="breadcrumbs" />
            </slot>
        </div>
        <div v-if="$slots['header-actions']" class="flex items-center gap-2">
            <slot name="header-actions" />
        </div>
    </header>
</template>
