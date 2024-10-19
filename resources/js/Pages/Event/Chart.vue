<script setup>
import colorLib from "@kurkle/color";
import helper from "@/helper";
import Chart from "chart.js/auto";
import { ref, reactive, onMounted } from "vue";

import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();

const chartElementRef = ref(null);
let chartElement;

const data = reactive({
    total: 159222,
    chart: {
        currentData: [
            0, 8, 12, 0, 0, 0, 14, 6, 14, 8, 14, 0, 0, 4, 18, 10, 7, 0, 8, 12,
            0, 0, 0, 14, 6, 14, 8, 14, 0, 0, 4, 18, 10, 7,
        ],
        currentLabel: "Current Revenue",
        labels: [
            "18 may",
            "19 may",
            "21 may",
            "22 may",
            "23 may",
            "24 may",
            "25 may",
            "26 may",
            "27 may",
            "28 may",
            "29 may",
            "30 may",
            "31 may",
            "06 jul",
            "08 jul",
            "11 jul",
            "13 jul",
            "18 may",
            "19 may",
            "21 may",
            "22 may",
            "23 may",
            "24 may",
            "25 may",
            "26 may",
            "27 may",
            "28 may",
            "29 may",
            "30 may",
            "31 may",
            "06 jul",
            "08 jul",
            "11 jul",
            "13 jul",
        ],
    },
});

const { range, event } = defineProps({
    range: Object,
    event: {
        type: String,
        required: true,
    },
});

const loadChartData = () => {
    setTimeout(() => {
        renderChart();
    }, 50);
};

const transparentize = (value, opacity) => {
    var alpha = opacity === undefined ? 0.5 : 1 - opacity;
    return colorLib(value).alpha(alpha).rgbString();
};

const renderChart = () => {
    // clear canva
    if (chartElement) {
        chartElement.data.labels = data.chart.labels;
        chartElement.data.datasets[0].data = data.chart.currentData;
        chartElement.update();
        return;
    }

    chartElement = new Chart(chartElementRef.value, {
        type: "line",
        data: {
            labels: data.chart.labels,
            datasets: [
                {
                    label: data.chart.currentLabel,
                    data: data.chart.currentData,
                    fill: true,
                    backgroundColor: transparentize("#A78BFA", 0.97),
                    borderColor: "#A78BFA",
                    borderRadius: 8,
                    borderWidth: 1.5,
                    tension: 0.1,
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
            maintainAspectRatio: true,
            aspectRatio: 4,
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
                            return `$${context.formattedValue} - ${context.label}`;
                        },
                        title: (context) => {
                            return event;
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

onMounted(() => {
    loadChartData();
});
</script>

<template>
    <div>
        <div>
            <div class="text-base font-medium text-zinc-600 mb-1">
                {{ event }}
            </div>
            <div
                class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
            >
                {{ helper.kFormatter(data.total) }}
            </div>
        </div>

        <canvas ref="chartElementRef"></canvas>
    </div>
</template>
