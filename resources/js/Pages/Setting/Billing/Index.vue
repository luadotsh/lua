<script setup>
import { ref, computed, onMounted, reactive } from "vue";
import { Head, usePage, Link, useForm } from "@inertiajs/vue3";

import {
    PhInfo,
    PhLink,
    PhCursorClick,
    PhTag,
    PhGlobe,
    PhUsers,
} from "@phosphor-icons/vue";

import AppLayout from "@/Layouts/Master.vue";
import Button from "@/Components/Button.vue";
import Chart from "@/Components/Chart.vue";

const usage = computed(() => usePage().props.usage);

const currentChart = ref("links");

const chartDataLinks = reactive({
    total: 0,
    chart: {
        label: "",
        data: [],
        labels: [],
    },
});

const chartDataEvents = reactive({
    total: 0,
    chart: {
        label: "",
        data: [],
        labels: [],
    },
});

onMounted(() => {
    // links
    chartDataLinks.total = usage.value.links.chart.total;
    chartDataLinks.chart = usage.value.links.chart.chart;

    // events
    chartDataEvents.total = usage.value.events.chart.total;
    chartDataEvents.chart = usage.value.events.chart.chart;
});

const links = computed(() => {
    return `${usage.value.links.remaining} remaining of ${usage.value.links.limit}`;
});

const events = computed(() => {
    return `${usage.value.events.remaining} remaining of ${usage.value.events.limit}`;
});

const users = computed(() => {
    return `${usage.value.users.remaining} remaining of ${usage.value.users.limit}`;
});

const domains = computed(() => {
    return `${usage.value.domains.remaining} remaining of ${usage.value.domains.limit}`;
});

const tags = computed(() => {
    return `${usage.value.tags.remaining} remaining of ${usage.value.tags.limit}`;
});
</script>

<template>
    <Head title="Pricing - Changelogfy" />

    <AppLayout>
        <div class="space-y-4">
            <div
                class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden px-6 py-8 text-zinc-800 dark:text-zinc-300"
            >
                <div class="flex flex-col justify-center space-y-4">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="page-title">Plan and Usage</h1>
                        </div>

                        <Button
                            v-if="usage.billing.has_subscription"
                            :href="route('setting.billing.portal')"
                            class="btn btn-secondary"
                        >
                            Manage Subscription
                        </Button>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            You are currently on the <b>{{ usage.plan.name }}</b
                            >. Current billing cycle:
                            <b>{{ usage.current_billing_cycle_formatted }}</b>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                    @click="currentChart = 'links'"
                    :class="{
                        'gap-x-4 gap-y-2 bg-white dark:bg-zinc-800 p-6 space-y-4 rounded-lg overflow-hidden cursor-pointer': true,
                        'border border-zinc-200 dark:border-zinc-700':
                            currentChart !== 'links',
                        'border-2 border-zinc-800 dark:border-violet-500':
                            currentChart === 'links',
                    }"
                >
                    <div class="space-y-2">
                        <PhLink class="h-6 w-6 text-zinc-400" />
                        <div class="text-zinc-800 dark:text-zinc-300">
                            Links created
                        </div>
                        <div
                            class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                        >
                            {{ usage.links.used }}
                        </div>
                    </div>
                    <div>
                        <div class="overflow-hidden rounded-full bg-zinc-200">
                            <div
                                class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                :style="`width: ${usage.links.percent}%`"
                            />
                        </div>
                        <div
                            class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                        >
                            {{ links }}
                        </div>
                    </div>
                </div>

                <div
                    @click="currentChart = 'events'"
                    :class="{
                        'gap-x-4 gap-y-2 bg-white dark:bg-zinc-800 p-6 space-y-4 rounded-lg overflow-hidden cursor-pointer': true,
                        'border border-zinc-200 dark:border-zinc-700':
                            currentChart !== 'events',
                        'border-2 border-zinc-800 dark:border-violet-500':
                            currentChart === 'events',
                    }"
                >
                    <div class="space-y-2">
                        <PhCursorClick class="h-6 w-6 text-zinc-400" />
                        <div class="text-zinc-800 dark:text-zinc-300">
                            Events tracked
                        </div>
                        <div
                            class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                        >
                            {{ usage.events.used }}
                        </div>
                    </div>
                    <div>
                        <div class="overflow-hidden rounded-full bg-zinc-200">
                            <div
                                class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                :style="`width: ${usage.events.percent}%`"
                            />
                        </div>
                        <div
                            class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                        >
                            {{ events }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-show="currentChart === 'links'">
                <Chart
                    title="Links"
                    :data="chartDataLinks"
                    type="bar"
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-4 bg-white dark:bg-zinc-800 p-4 mb-4"
                />
            </div>

            <div v-show="currentChart === 'events'">
                <Chart
                    title="Events"
                    :data="chartDataEvents"
                    type="bar"
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-4 bg-white dark:bg-zinc-800 p-4 mb-4"
                />
            </div>

            <div
                class="mx-auto grid grid-cols-1 gap-px bg-zinc-100 dark:bg-zinc-700 sm:grid-cols-3 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
            >
                <div class="bg-white dark:bg-zinc-800 p-4 space-y-4">
                    <div class="space-y-2">
                        <PhTag class="h-6 w-6 text-zinc-400" />
                        <div class="text-zinc-800 dark:text-zinc-300">Tags</div>
                        <div
                            class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                        >
                            {{ usage.tags.used }}
                        </div>
                    </div>
                    <div>
                        <div class="overflow-hidden rounded-full bg-zinc-200">
                            <div
                                class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                :style="`width: ${usage.tags.percent}%`"
                            />
                        </div>
                        <div
                            class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                        >
                            {{ tags }}
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 p-4 space-y-4">
                    <div class="space-y-2">
                        <PhGlobe class="h-6 w-6 text-zinc-400" />
                        <div class="text-zinc-800 dark:text-zinc-300">
                            Domains
                        </div>
                        <div
                            class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                        >
                            {{ usage.domains.used }}
                        </div>
                    </div>
                    <div>
                        <div class="overflow-hidden rounded-full bg-zinc-200">
                            <div
                                class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                :style="`width: ${usage.domains.percent}%`"
                            />
                        </div>
                        <div
                            class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                        >
                            {{ domains }}
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 p-4 space-y-4">
                    <div class="space-y-2">
                        <PhUsers class="h-6 w-6 text-zinc-400" />
                        <div class="text-zinc-800 dark:text-zinc-300">
                            Users
                        </div>
                        <div
                            class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                        >
                            {{ usage.users.used }}
                        </div>
                    </div>
                    <div>
                        <div class="overflow-hidden rounded-full bg-zinc-200">
                            <div
                                class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                :style="`width: ${usage.users.percent}%`"
                            />
                        </div>
                        <div
                            class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                        >
                            {{ users }}
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden p-4"
            >
                <div class="flex items-center w-full space-x-2">
                    <div class="flex-shrink-0">
                        <PhInfo class="h-5 w-5 text-zinc-400" />
                    </div>
                    <div
                        class="flex w-full justify-between items-center space-x-2"
                    >
                        <div
                            class="text-sm font-medium text-zinc-800 dark:text-white"
                        >
                            For higher limits, upgrade to the
                            {{ usage.plan.next_tier.name }} plan.
                        </div>
                        <div>
                            <Button
                                :href="
                                    usage.billing.has_subscription
                                        ? route('setting.billing.portal')
                                        : route('setting.billing.upgrade')
                                "
                                class="btn btn-primary"
                            >
                                Upgrade
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
