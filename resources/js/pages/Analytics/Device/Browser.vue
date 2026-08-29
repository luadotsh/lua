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

const browsers = ref(null);

const loadData = () => {
    axios
        .get(statistics.url({ query: { ...props.range, metric: "browsers" } }))
        .then((response) => {
            browsers.value = response.data.map((item: Record<string, unknown>) => {
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
