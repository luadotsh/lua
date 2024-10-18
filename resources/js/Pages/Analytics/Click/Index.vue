<script setup>
import helper from "@/helper";
import Chart from "chart.js/auto";
import { ref, watch, reactive } from "vue";

const chartElementRef = ref(null);
let chartElement;

const data = reactive({
    total: 0,
    totalPrevious: 0,
    chart: {
        label: "",
        data: [],
        labels: [],
    },
});

const props = defineProps({
    dateRange: Object,
});

const loadChartData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.dateRange,
                metric: "clicks",
            },
        })
        .then((response) => {
            data.total = response.data.total;
            data.totalPrevious = response.data.totalPrevious;
            data.chart = response.data.chart;

            renderChart();
        });
};

const renderChart = () => {
    // clear canva
    if (chartElement) {
        chartElement.data.labels = data.chart.labels;
        chartElement.data.datasets[0].data = data.chart.data;
        chartElement.data.datasets[0].label = data.chart.label;
        chartElement.update();
        return;
    }

    chartElement = new Chart(chartElementRef.value, {
        type: "bar",
        data: {
            labels: data.chart.labels,
            datasets: [
                {
                    label: data.chart.label,
                    data: data.chart.data,
                    fill: true,
                    backgroundColor: helper.transparentize("#7100fd", 0.2),
                    borderColor: "#7100fd",
                    borderRadius: 4,
                    borderSkipped: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },

                tooltip: {
                    displayColors: false,
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                },
                y: {
                    display: false,
                },
            },
        },
    });
};

watch(
    props.dateRange,
    () => {
        loadChartData();
    },
    { immediate: true }
);
</script>

<template>
    <div class="col-span-12 card p-4">
        <div class="flex items-center justify-between mb-12">
            <div>
                <div
                    class="capitalize text-base font-semibold text-black dark:text-white"
                >
                    Clicks
                </div>
            </div>

            <div class="flex items-end">
                <div class="flex flex-row items-center">
                    <div
                        class="text-3xl font-extrabold leading-none text-zinc-800 dark:text-zinc-100"
                    >
                        {{ helper.kFormatter(data.total) }}
                    </div>
                    <div
                        :class="{
                            'font-bold ml-2  text-sm': true,
                            'text-zinc-400': data.totalPrevious == 0,
                            'text-green-400': data.totalPrevious > 0,
                            'text-red-400': data.totalPrevious < 0,
                        }"
                    >
                        <span v-if="data.totalPrevious > 0" class="font-bold">
                            +
                        </span>
                        {{ data.totalPrevious }}%
                    </div>
                </div>
            </div>
        </div>

        <div
            class="w-full"
            style="position: relative; height: 40vh; width: 80vw"
        >
            <canvas
                ref="chartElementRef"
                style="width: 100%; height: 100%"
            ></canvas>
        </div>
    </div>
</template>
