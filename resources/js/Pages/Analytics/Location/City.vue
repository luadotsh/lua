<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    dateRange: Object,
});

const cities = ref(null);

const loadData = async () => {
    return await axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.dateRange,
                metric: "cities",
            },
        })
        .then((response) => {
            cities.value = response.data;
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
    <Table v-if="cities" :data="cities" />
</template>
