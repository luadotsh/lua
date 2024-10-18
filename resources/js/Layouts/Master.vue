<script setup>
import { computed, watch, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";

import Sidebar from "./Sidebar.vue";
import Banner from "@/Components/Banner.vue";
import Announcement from "@/Components/Announcement.vue";
import Sandbox from "@/Components/Sandbox.vue";

import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();

defineProps({
    overflow: {
        type: Boolean,
        default: true,
    },

    fluid: {
        type: Boolean,
        default: true,
    },
});

const user = computed(() => usePage().props.auth.user);
</script>

<template>
    <Banner />

    <div
        class="h-screen overflow-hidden flex flex-col bg-zinc-100 dark:bg-zinc-900"
    >
        <Sidebar />

        <div class="lg:pl-64 pl-2 pr-2 py-2 overflow-hidden w-full h-full">
            <Sandbox class="mb-2 rounded-md" />
            <Announcement class="mb-2 rounded-md" />
            <div
                class="bg-white dark:bg-zinc-800 w-full h-full border border-zinc-300 dark:border-zinc-700 rounded-lg shadow-lg dark:shadow-none overflow-hidden"
            >
                <div
                    :class="{
                        'overflow-x-hidden h-full': true,
                        'overflow-y-auto': overflow,
                        'overflow-y-hidden': !overflow,
                    }"
                >
                    <div class="h-full flex flex-col justify-between">
                        <div
                            v-if="$slots.header"
                            class="px-4 py-2.5 sticky top-0 bg-white dark:bg-zinc-800 z-20 border-b border-zinc-100 dark:border-zinc-700 lg:min-h-[60px] lg:h-[60px] flex items-center w-full"
                        >
                            <slot name="header"></slot>
                        </div>

                        <div
                            v-if="$slots.subheader"
                            class="sticky top-[60px] bg-white dark:bg-zinc-800 z-10"
                        >
                            <slot name="subheader"></slot>
                        </div>

                        <div
                            :class="{
                                'flex flex-col flex-1 w-full': true,
                                'px-4 py-4 sm:px-0 sm:py-0': fluid,
                                'max-w-5xl mx-auto p-2 sm:p-12': !fluid,
                                'overflow-hidden': !overflow,
                            }"
                        >
                            <slot />
                        </div>
                        <div
                            v-if="$slots.pagination"
                            class="px-4 sticky bottom-0 border-t border-zinc-100 dark:border-zinc-700 bg-white dark:bg-zinc-900"
                        >
                            <slot name="pagination"></slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
