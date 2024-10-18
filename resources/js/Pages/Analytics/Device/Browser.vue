<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    dateRange: Object,
});

const browsers = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.dateRange,
                metric: "browsers",
            },
        })
        .then((response) => {
            browsers.value = response.data.map((item) => {
                return {
                    ...item,
                    browser: item.x,
                };
            });
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
    <Table v-if="browsers" :data="browsers" />
</template>
