<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    range: Object,
});

const data = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.range,
                metric: "utm-sources",
            },
        })
        .then((response) => {
            data.value = response.data;
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
    <Table
        v-if="data"
        :data="data"
        title="Sources"
        :titles="{ key: 'Sources', value: 1 }"
    />
</template>
