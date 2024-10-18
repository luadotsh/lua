<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, computed, reactive } from "vue";

import Layout from "@/Layouts/Master.vue";
import Header from "./Header.vue";

import Link from "./Link/Index.vue";
import Click from "./Click/Index.vue";
import Source from "./Source/Index.vue";
import Device from "./Device/Index.vue";
import Location from "./Location/Index.vue";

const range = ref(null);

const setRange = (data) => {
    range.value = data;
};
</script>

<template>
    <Head title="Analytics" />

    <Layout>
        <template #header>
            <div class="sm:flex sm:items-center">
                <div class="flex items-center space-x-2">
                    <div>
                        <h1 class="page-title">Analytics</h1>
                    </div>

                    <Header @update="setRange" />
                </div>
            </div>
        </template>
        <div v-if="range">
            <Click
                :dateRange="range"
                class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-4 bg-white dark:bg-zinc-800 px-4 py-4"
            />

            <div
                class="mx-auto grid grid-cols-1 gap-px bg-zinc-100 dark:bg-zinc-700 sm:grid-cols-2 border-y border-zinc-100 dark:border-zinc-700"
            >
                <Link
                    :dateRange="range"
                    class="flex flex-col gap-4 bg-white dark:bg-zinc-800 px-4 py-4"
                />
                <Source
                    :dateRange="range"
                    class="flex flex-col gap-4 bg-white dark:bg-zinc-800 px-4 py-4"
                />

                <Location
                    :dateRange="range"
                    class="flex flex-col gap-4 bg-white dark:bg-zinc-800 px-4 py-4"
                />
                <Device
                    :dateRange="range"
                    class="flex flex-col gap-4 bg-white dark:bg-zinc-800 px-4 py-4"
                />
            </div>
        </div>
    </Layout>
</template>
