<script setup>
import { onMounted, ref } from "vue";
import { usePage, Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/Master.vue";
import Button from "@/Components/Button.vue";
// import Highcharts from "highcharts";

import { PhInfo, PhCaretRight } from "@phosphor-icons/vue";

const chart = ref(null);
const user = usePage().props.auth.user;

const websites = usePage().props.websites;
const pageViews = usePage().props.pageViews;

const modalUpgrade = ref(null);

const data = ref({
    startDate: null,
    endDate: null,
    days: [],
    views: [],
});

const renderChart = () => {
    // Highcharts.chart(chart.value, {
    //     chart: {
    //         type: "column",
    //         backgroundColor: "transparent",
    //     },
    //     time: {
    //         timezone: user.timezone.value,
    //     },
    //     accessibility: {
    //         enabled: false,
    //     },
    //     credits: { enabled: false },
    //     title: false,
    //     subtitle: false,
    //     legend: false,
    //     xAxis: {
    //         categories: data.value.days,
    //         crosshair: true,
    //     },
    //     yAxis: {
    //         title: false,
    //     },
    //     tooltip: {
    //         headerFormat:
    //             '<span style="font-size:10px">{point.key}</span><table>',
    //         pointFormat:
    //             '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
    //             '<td style="padding:0"><b>{point.y} </b></td></tr>',
    //         footerFormat: "</table>",
    //         shared: true,
    //         useHTML: true,
    //     },
    //     plotOptions: {
    //         column: {
    //             pointPadding: 0.2,
    //             borderWidth: 0,
    //         },
    //     },
    //     series: [
    //         {
    //             name: "Views",
    //             data: data.value.views,
    //         },
    //     ],
    // });
};

onMounted(() => {
    // format data for highcharts

    data.value.startDate = dayjs(Object.keys(pageViews)[0]).format(
        "MMM DD, YYYY"
    );
    data.value.endDate = dayjs(
        Object.keys(pageViews)[Object.keys(pageViews).length - 1]
    ).format("MMM DD, YYYY");

    for (const [date, views] of Object.entries(pageViews)) {
        data.value.days.push(dayjs(date).format("DD MMM "));
        data.value.views.push(views);
    }

    // renderChart();
});

const formatDate = (date) => {
    return dayjs(date).format("MMM DD, YYYY");
};

const openModalUpgrade = () => {
    modalUpgrade.value.open();
};
</script>
<template>
    <Head title="Billing" />
    <Layout>
        <div class="space-y-4">
            <div v-if="user.plan.name == 'trial'" class="card p-4">
                <div class="flex items-center w-full space-x-2">
                    <div class="flex-shrink-0">
                        <PhInfo
                            class="h-5 w-5 text-gray-400"
                            aria-hidden="true"
                        />
                    </div>
                    <div
                        class="flex w-full justify-between items-center space-x-2"
                    >
                        <div
                            class="text-sm font-medium text-zinc-800 dark:text-white"
                        >
                            You are currently on a trial period until
                            {{ formatDate(user.trial_ends_at) }}.
                        </div>
                        <div>
                            <button
                                @click="openModalUpgrade"
                                type="button"
                                class="flex items-center space-x-1 rounded-md bg-green-500 px-4 py-1.5 text-sm font-semibold text-white hover:bg-green-600 focus:outline-none"
                            >
                                <div>Upgrade Now</div>
                                <PhCaretRight
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="card p-4">
                <div class="flex flex-col space-y-4">
                    <div class="px-4 sm:px-0">
                        <h3
                            class="text-lg font-medium text-zinc-800 dark:text-gray-100"
                        >
                            <div class="flex items-center">Billing Portal</div>
                        </h3>
                        <p
                            class="mt-1 text-sm text-zinc-700 dark:text-gray-300 font-medium"
                        >
                            Click on the link below to manage your subscription,
                            update your credit card and see your invoices
                        </p>
                    </div>
                    <div class="px-4 sm:px-0">
                        <Button :href="route('billing.redirect-to-portal')">
                            Go To Portal
                        </Button>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="mb-4 text-sm">
                    Event usage: {{ data.startDate }} — {{ data.endDate }} (UTC)
                </div>
                <div ref="chart" />
            </div>
        </div>
    </Layout>
</template>
