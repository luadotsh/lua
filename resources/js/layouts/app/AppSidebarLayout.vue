<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, useSlots } from 'vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import Toast from '@/components/Toast.vue';
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar';
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

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    fullWidth: false,
    title: '',
    total: null,
});

const slots = useSlots();

const hasHeader = computed(
    () =>
        Boolean(props.title) ||
        props.breadcrumbs.length > 0 ||
        Boolean(slots.header) ||
        Boolean(slots['header-actions']),
);
</script>

<template>
    <SidebarProvider :default-open="isOpen">
        <AppSidebar />
        <!--
            The header is a fixed strip and the content scrolls beneath it, so
            the screen's own title stays put while a long list moves. It is also
            what lets a sticky table header pin directly below it rather than
            fighting it for the top of the page.
        -->
        <SidebarInset class="flex min-h-0 flex-1 flex-col overflow-hidden">
            <!--
                Screens that carry their own heading in the content — settings —
                pass nothing here, and get no empty bar. They still need a way to
                open the drawer below `md`, hence the floating trigger.
            -->
            <AppSidebarHeader
                v-if="hasHeader"
                :breadcrumbs="breadcrumbs"
                :title="title"
                :total="total"
            >
                <template v-if="$slots.header" #header>
                    <slot name="header" />
                </template>
                <template v-if="$slots['header-actions']" #header-actions>
                    <slot name="header-actions" />
                </template>
            </AppSidebarHeader>
            <SidebarTrigger
                v-else
                class="absolute left-4 top-3 z-30 size-9 rounded-md border border-border bg-card text-foreground shadow-xs md:hidden"
            />
            <!--
                A full-width screen scrolls nothing here: its table owns both
                axes, so the metrics and chart above it stay put while you reach
                the last column. Scrolling here instead would drag them sideways
                along with the table.

                Every other screen scrolls normally. `pb-px` is not decoration:
                Inertia's InfiniteScroll watches a zero-height element after the
                list, and an element flush with the scrollport's bottom edge
                never counts as intersecting — so the next page never loaded.
                The padding leaves it just inside.
            -->
            <div
                :class="
                    fullWidth
                        ? 'flex min-h-0 min-w-0 flex-1 overflow-hidden'
                        : 'min-h-0 flex-1 overflow-auto pb-px'
                "
            >
                <div
                    :class="
                        fullWidth
                            ? 'flex min-w-0 flex-1 flex-col'
                            : 'mx-auto w-full max-w-7xl'
                    "
                >
                    <slot />
                </div>
            </div>
        </SidebarInset>
    </SidebarProvider>
    <Toast />
</template>
