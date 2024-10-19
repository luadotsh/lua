<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    range: Object,
});

const devices = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.range,
                metric: "devices",
            },
        })
        .then((response) => {
            devices.value = response.data;
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
    <Table v-if="devices" :data="devices" :capitalize="true" />
</template>
