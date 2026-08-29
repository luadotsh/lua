<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import Toast from '@/components/Toast.vue';
import { SidebarInset, SidebarProvider } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

const page = usePage();
const isOpen = page.props.sidebarOpen;

type Props = {
    breadcrumbs?: BreadcrumbItem[];
    fullWidth?: boolean;
    /** Rendered in the header. Screens with a plain title need nothing else. */
    title?: string;
    /** Item count shown in brackets next to the title. Hidden when 0 or null. */
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
    <SidebarProvider :default-open="isOpen">
        <AppSidebar />
        <SidebarInset class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" :title="title" :total="total">
                <template v-if="$slots.header" #header>
                    <slot name="header" />
                </template>
                <template v-if="$slots['header-actions']" #header-actions>
                    <slot name="header-actions" />
                </template>
            </AppSidebarHeader>
            <div
                :class="
                    fullWidth
                        ? 'flex min-h-0 flex-1 flex-col'
                        : 'mx-auto w-full max-w-7xl'
                "
            >
                <slot />
            </div>
        </SidebarInset>
    </SidebarProvider>
    <Toast />
</template>
