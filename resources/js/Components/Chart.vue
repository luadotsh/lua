<script setup>
import colorLib from "@kurkle/color";
import helper from "@/helper";
import Chart from "chart.js/auto";
import { RadioGroup, RadioGroupOption } from "@headlessui/vue";

import { ref, reactive, watch } from "vue";

import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();

const types = reactive([
    { value: "bar", label: "Bar" },
    { value: "line", label: "Line" },
]);

const props = defineProps({
    type: {
        type: String,
        default: "bar",
        required: false,
    },

    title: {
        type: String,
        required: true,
    },

    data: {
        type: Object,
        required: true,
    },
});

const chartType = ref(types.find((type) => type.value === props.type));
const chartElementRef = ref(null);
let chartElement;

const data = reactive({
    total: props.data.total,
    chart: props.data.chart,
});

const transparentize = (value, opacity) => {
    var alpha = opacity === undefined ? 0.5 : 1 - opacity;
    return colorLib(value).alpha(alpha).rgbString();
};

const renderChart = (force = false) => {
    // clear canva
    if (chartElement && !force) {
        chartElement.data.labels = data.chart.labels;
        chartElement.data.datasets[0].data = data.chart.data;
        chartElement.data.datasets[0].label = data.chart.label;
        chartElement.update();
        return;
    }

    if (force) {
        chartElement.destroy();
    }

    chartElement = new Chart(chartElementRef.value, {
        type: chartType.value.value,
        data: {
            labels: data.chart.labels,
            datasets: [
                {
                    label: data.chart.label,
                    data: data.chart.data,
                    fill: true,
                    backgroundColor: transparentize("#A78BFA", 0.97),
                    borderColor: "#A78BFA",
                    borderRadius: 4,
                    borderWidth: 1.5,
                    tension: 0.3,
                    pointStyle: "circle",
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: "#ffffff",
                    pointHoverBorderColor: "#A78BFA",
                    pointHoverBorderWidth: 2,
                    spanGaps: true,
                },
            ],
        },
        options: {
            clip: false,
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 4,
            layout: {
                padding: {
                    top: 10,
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    usePointStyle: true,
                    boxWidth: 10,
                    boxHeight: 10,
                    boxPadding: 4,
                    backgroundColor: "#ffffff",
                    titleFont: {
                        weight: "500",
                        size: 14,
                    },
                    titleColor: "#000000",
                    bodyColor: "#000000",
                    bodyFont: {
                        weight: "500",
                        size: 12,
                    },

                    padding: {
                        left: 10,
                        right: 10,
                        top: 5,
                        bottom: 5,
                    },

                    borderColor: "rgba(0,0,0,0.1)",
                    borderWidth: 1,
                    displayColors: true,

                    callbacks: {
                        label: (context) => {
                            return `${context.formattedValue} ${props.title}`;
                        },
                        labelColor: function (context) {
                            return {
                                borderColor: context.dataset.borderColor,
                                backgroundColor: "#ffffff",
                            };
                        },

                        labelTextColor: function (context) {
                            return context.dataset.pointHoverBorderColor;
                        },
                    },
                },
            },

            interaction: {
                mode: "index",
                intersect: false,
            },

            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                        align: "inner",
                        color: "#9ca3af",
                        callback: (value, index, values) => {
                            if (index == 0 || index == values.length - 1) {
                                return data.chart.labels[index];
                            } else {
                                return "";
                            }
                        },
                    },

                    grid: {
                        display: true,
                        drawTicks: false,
                        color: isDarkTheme ? "#374151" : "#ddd", // for the grid lines
                        tickBorderDash: [6, 7], // also for the tick, if long enough
                        tickLength: 10, // just to see the dotted line
                        tickWidth: 2,
                        drawOnChartArea: false,
                    },
                    border: {
                        dash: [4, 4],
                        color: "transparent",
                    },
                    beginAtZero: true,
                },
                y: {
                    display: false,
                    beginAtZero: true,
                },
            },
        },
    });
};

watch(chartType, () => {
    renderChart(true);
});

watch(
    props,
    () => {
        data.total = props.data.total;
        data.chart = props.data.chart;
        renderChart();
    },
    {
        deep: true,
    }
);
</script>

<template>
    <div
        class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
    >
        <div class="w-full flex justify-between">
            <div>
                <div
                    class="text-base font-medium text-zinc-800 dark:text-zinc-300 mb-1"
                >
                    {{ props.title }}
                </div>
                <div
                    class="text-2xl font-semibold text-zinc-800 dark:text-white"
                >
                    {{ helper.kFormatter(data.total) }}
                </div>
            </div>
            <div class="flex justify-center">
                <fieldset>
                    <RadioGroup
                        v-model="chartType"
                        class="grid grid-cols-2 gap-x-1 rounded-md p-1 text-center text-xs font-semibold leading-5 ring-1 ring-inset ring-zinc-200 dark:ring-zinc-700"
                    >
                        <RadioGroupOption
                            as="template"
                            v-for="option in types"
                            :key="option.value"
                            :value="option"
                            v-slot="{ checked }"
                        >
                            <div
                                :class="[
                                    checked
                                        ? 'bg-zinc-800 dark:bg-zinc-900 text-white'
                                        : 'text-zinc-500 dark:text-zinc-300',
                                    'cursor-pointer rounded px-2.5 py-1',
                                ]"
                            >
                                {{ option.label }}
                            </div>
                        </RadioGroupOption>
                    </RadioGroup>
                </fieldset>
            </div>
        </div>

        <div
            class="w-full"
            style="position: relative; height: 260px; width: 100%"
        >
            <canvas
                ref="chartElementRef"
                style="width: 100%; height: 100%"
            ></canvas>
        </div>
    </div>
</template>
