<script setup lang="ts">
import { reactive, watch } from "vue";
import Chart from "@/components/Chart.vue";

import * as analyticsRoute from "@/routes/analytics";

interface Range {
    start: string;
    end: string;
    timezone: string;
    group: string;
}

interface ChartData {
    total: number;
    chart: {
        label: string;
        data: number[];
        labels: string[];
    };
}

const props = defineProps<{
    range: Range;
}>();

const data = reactive<ChartData>({
    total: 0,
    chart: {
        label: "",
        data: [],
        labels: [],
    },
});

const refresh = () => {
    axios
        .get(analyticsRoute.statistics.url({
            query: {
                ...props.range,
                metric: "qrScans",
            },
        }))
        .then((response) => {
            data.total = response.data.total;
            data.chart = response.data.chart;
        });
};

watch(
    props,
    () => {
        refresh();
    },
    {
        immediate: true,
    }
);
</script>

<template>
    <Chart title="QR Scans" :data="data" />
</template>
