<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { onMounted, ref, computed, reactive } from "vue";
import date from "@/date";

import RangePicker from "@/Components/RangePicker.vue";
import Layout from "@/Layouts/Master.vue";

import Link from "./Link/Index.vue";
import Event from "./Event/Index.vue";
import Source from "./Source/Index.vue";
import Device from "./Device/Index.vue";
import Location from "./Location/Index.vue";

const range = ref({
    timezone: date.getUserTimezone(),
    group: "day",
    start: usePage().props.start,
    end: usePage().props.end,
});

const setRange = (data) => {
    range.value = {
        timezone: date.getUserTimezone(),
        group: data.group,
        start: data.start,
        end: data.end,
    };
};
</script>

<template>
    <Head title="Analytics" />

    <Layout>
        <template #header>
            <div class="w-full">
                <div
                    class="flex flex-col lg:flex-row space-y-4 lg:space-y-0 sm:items-center"
                >
                    <div class="sm:flex-auto">
                        <h1 class="page-title">Analytics</h1>
                    </div>
                    <div>
                        <RangePicker
                            v-model:range="range"
                            @update:range="setRange"
                            placement="bottom-end"
                        />
                    </div>
                </div>
            </div>
        </template>
        <div v-if="range">
            <Event
                :range="range"
                class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-4 bg-white dark:bg-zinc-800 p-4 mb-4"
            />

            <div class="mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Link
                    :range="range"
                    class="bg-white dark:bg-zinc-800 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
                />
                <Source
                    :range="range"
                    class="bg-white dark:bg-zinc-800 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
                />

                <Location
                    :range="range"
                    class="bg-white dark:bg-zinc-800 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
                />
                <Device
                    :range="range"
                    class="bg-white dark:bg-zinc-800 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
                />
            </div>
        </div>
    </Layout>
</template>
