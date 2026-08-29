<script setup lang="ts">
import { ref, reactive, watch } from "vue";
import Chart from "@/components/Chart.vue";
import { statistics } from "@/routes/analytics";

interface Range {
    timezone: string;
    group: string;
    start: string | null;
    end: string | null;
}

const props = defineProps<{
    range: Range;
}>();

const data = reactive({
    total: 0,
    chart: {
        label: "",
        data: [] as number[],
        labels: [] as string[],
    },
});

const refresh = () => {
    axios
        .get(statistics.url({ query: { ...props.range, metric: "events" } }))
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
    <Chart title="Events" :data="data" />
</template>
