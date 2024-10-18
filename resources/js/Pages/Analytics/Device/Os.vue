<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    dateRange: Object,
});

const os = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.dateRange,
                metric: "os",
            },
        })
        .then((response) => {
            os.value = response.data;
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
    <Table v-if="os" :data="os" />
</template>
