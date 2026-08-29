<script setup lang="ts">
import Table from "@/components/Table.vue";
import { ref, watch } from "vue";
import { statistics } from "@/routes/analytics";

interface Range {
    timezone: string;
    group: string;
    start: string | null;
    end: string | null;
}

const props = defineProps<{
    range: Range;
}>();

const cities = ref(null);

const loadData = async () => {
    return await axios
        .get(statistics.url({ query: { ...props.range, metric: "cities" } }))
        .then((response) => {
            cities.value = response.data;
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
    <Table v-if="cities" :data="cities" />
</template>
