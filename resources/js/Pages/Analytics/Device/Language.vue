<script setup>
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";

const props = defineProps({
    range: Object,
});

const languages = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.range,
                metric: "languages",
            },
        })
        .then((response) => {
            languages.value = response.data;
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
    <Table v-if="languages" :data="languages" :uppercase="true" />
</template>
