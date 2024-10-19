<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    range: Object,
});

const browsers = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.range,
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
    props,
    () => {
        loadData();
    },
    { immediate: true }
);
</script>
<template>
    <Table v-if="browsers" :data="browsers" />
</template>
