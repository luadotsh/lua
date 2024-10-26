<script setup>
import { ref, reactive, watch } from "vue";
import Chart from "@/Components/Chart.vue";

const data = reactive({
    total: 0,
    chart: {
        label: "",
        data: [],
        labels: [],
    },
});

const props = defineProps({
    range: Object,
});

const refresh = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.range,
                metric: "events",
            },
        })
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
