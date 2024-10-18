<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    dateRange: Object,
});

const data = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.dateRange,
                metric: "utm-mediums",
            },
        })
        .then((response) => {
            data.value = response.data;
        });
};

watch(
    props.dateRange,
    () => {
        loadData();
    },
    { immediate: true }
);
</script>
<template>
    <Table v-if="data" :data="data" />
</template>
