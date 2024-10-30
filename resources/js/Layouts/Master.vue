<script setup>
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
});
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
                    <div
                        class="h-full flex flex-col justify-between max-w-7xl mx-auto"
                    >
                        <div
                            v-if="$slots.header"
                            class="p-4 lg:p-8 bg-white dark:bg-zinc-800 flex items-center w-full z-40"
                        >
                            <slot name="header"></slot>
                        </div>

                        <div
                            :class="{
                                'flex flex-col flex-1 w-full p-4 lg:p-8': true,
                                'overflow-hidden': !overflow,
                            }"
                        >
                            <slot />
                        </div>
                        <div
                            v-if="$slots.pagination"
                            class="px-4 lg:px-8 bg-white dark:bg-zinc-800"
                        >
                            <slot name="pagination"></slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
