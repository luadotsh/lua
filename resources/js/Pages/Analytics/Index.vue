<script setup lang="ts">
import { Head, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import date from "@/date";

import RangePicker from "@/components/RangePicker.vue";
import AppLayout from "@/layouts/AppLayout.vue";

import Link from "./Link/Index.vue";
import Event from "./Event/Index.vue";
import Source from "./Source/Index.vue";
import Device from "./Device/Index.vue";
import Location from "./Location/Index.vue";

interface Range {
    timezone: string;
    group: string;
    start: string | null;
    end: string | null;
}

const range = ref<Range>({
    timezone: date.getUserTimezone(),
    group: "day",
    start: usePage().props.start as string | null,
    end: usePage().props.end as string | null,
});

const setRange = (data: Range) => {
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

    <AppLayout>
        <template #header-right>
            <RangePicker
                v-model:range="range"
                @update:range="setRange"
                placement="bottom-end"
            />
        </template>

        <div class="p-4 lg:p-6">
            <div class="mb-4">
                <h1 class="page-title">Analytics</h1>
            </div>

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
        </div>
    </AppLayout>
</template>
