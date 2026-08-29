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

const data = ref(null);

const loadData = () => {
    axios
        .get(statistics.url({ query: { ...props.range, metric: "utm-sources" } }))
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
