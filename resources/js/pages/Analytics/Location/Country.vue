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

const countries = ref(null);

const loadData = () => {
    axios
        .get(statistics.url({ query: { ...props.range, metric: "countries" } }))
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
