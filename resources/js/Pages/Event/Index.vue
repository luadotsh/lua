<script setup>
import { reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Layout from "@/Layouts/Master.vue";

import Pagination from "@/Components/Pagination.vue";
import Header from "./Header.vue";
import Chart from "./Chart.vue";

const props = defineProps({
    start: String,
    end: String,
    table: Object,
    hasData: Boolean,
});

const range = ref({
    start: props.start,
    end: props.end,
});

const columns = reactive([
    {
        key: "event",
        label: "Event",
        show: true,
    },
    {
        key: "link",
        label: "Link",
        show: true,
    },
    {
        key: "country",
        label: "Country",
        show: true,
    },
    {
        key: "region",
        label: "Region",
        show: true,
    },
    {
        key: "city",
        label: "City",
        show: false,
    },
    {
        key: "device",
        label: "Device",
        show: true,
    },
    {
        key: "browser",
        label: "Browser",
        show: true,
    },
    {
        key: "os",
        label: "OS",
        show: true,
    },
    {
        key: "language",
        label: "Language",
        show: false,
    },
    {
        key: "utm_medium",
        label: "Utm Medium",
        show: false,
    },
    {
        key: "utm_source",
        label: "Utm Source",
        show: false,
    },
    {
        key: "utm_campaign",
        label: "Utm Campaign",
        show: false,
    },
    {
        key: "utm_content",
        label: "Utm Content",
        show: false,
    },
    {
        key: "utm_term",
        label: "Utm Term",
        show: false,
    },
    {
        key: "referer",
        label: "Referer",
        show: false,
    },
]);

const refresh = (value) => {
    range.value = value;
    router.visit(
        route("events.index", {
            start: range.value.start,
            end: range.value.end,
        }),
        {
            method: "get",
            replace: true,
        }
    );
};
</script>

<template>
    <Layout>
        <template #header>
            <Header
                :columns="columns"
                :range="range"
                @update:columns="columns = $event"
                @update:range="refresh"
            />
        </template>
        <div class="space-y-4">
            <div
                class="mx-auto grid grid-cols-1 gap-px bg-zinc-100 dark:bg-zinc-700 sm:grid-cols-2 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
            >
                <Chart
                    :range="range"
                    event="Clicks"
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white dark:bg-zinc-800 px-4 py-4"
                />
                <Chart
                    :range="range"
                    event="QR Scans"
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 bg-white dark:bg-zinc-800 px-4 py-4"
                />
            </div>

            <div
                class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
            >
                <div class="px-4 sm:px-0">
                    <div class="flex flex-col">
                        <div
                            class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8"
                        >
                            <div
                                class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8"
                            >
                                <div class="table-wrapper">
                                    <table class="table">
                                        <thead class="table-thead">
                                            <tr class="table-tr">
                                                <template
                                                    v-for="column in columns"
                                                    :key="column.key"
                                                >
                                                    <th
                                                        v-if="column.show"
                                                        scope="col"
                                                        class="table-th"
                                                    >
                                                        {{ column.label }}
                                                    </th>
                                                </template>
                                            </tr>
                                        </thead>
                                        <tbody class="table-tbody">
                                            <tr
                                                as="tr"
                                                v-for="event in table.data"
                                                :key="event.id"
                                                class="table-tr"
                                            >
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'event' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.event }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'link' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.link.link }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'country' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.country }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'region' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.region }}
                                                </td>

                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'city' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.city }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'device' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.device }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'browser' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.browser }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'os' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.os }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'language' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.language }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'utm_medium' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.utm_medium }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'utm_source' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.utm_source }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'utm_campaign' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.utm_campaign }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'utm_content' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.utm_content }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'utm_term' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.utm_term }}
                                                </td>
                                                <td
                                                    v-if="
                                                        columns.find(
                                                            (c) =>
                                                                c.key ===
                                                                    'referer' &&
                                                                c.show
                                                        )
                                                    "
                                                    class="table-td"
                                                >
                                                    {{ event.referer }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template v-if="table.next_page_url" #pagination>
            <Pagination :data="table" />
        </template>
    </Layout>
</template>
