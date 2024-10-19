<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    range: Object,
});

const countries = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.range,
                metric: "countries",
            },
        })
        .then((response) => {
            countries.value = response.data;
        });
};

watch(
    props,
    () => {
        loadData();
    },
    { immediate: true }
);
</script>
<template>
    <Table v-if="countries" :data="countries" :country="true" />
</template>
