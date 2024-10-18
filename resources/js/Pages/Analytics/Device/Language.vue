<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    dateRange: Object,
});

const languages = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.dateRange,
                metric: "languages",
            },
        })
        .then((response) => {
            languages.value = response.data;
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
    <Table v-if="languages" :data="languages" :uppercase="true" />
</template>
