<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    range: Object,
});

const regions = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.range,
                metric: "regions",
            },
        })
        .then((response) => {
            regions.value = response.data;
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
    <Table v-if="regions" :data="regions" />
</template>
