<script setup>
import { ref, watch } from "vue";
import Tab from "@/Components/Tab.vue";
import Table from "@/Components/Table.vue";

const tab = ref("links");
const tabs = ["links"];

const setTab = (value) => {
    tab.value = value;
};

const props = defineProps({
    dateRange: Object,
});

const data = ref(null);

const loadData = () => {
    axios
        .get(route("analytics.statistics"), {
            params: {
                ...props.dateRange,
                metric: "links",
            },
        })
        .then((response) => {
            data.value = response.data;
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
    <div class="col-span-12 lg:col-span-6 card p-4 min-h-[450px]">
        <Tab :currentTab="tab" :tabs="tabs" @update="setTab" title="Links" />

        <div class="mt-4">
            <Table v-if="data" :data="data" :favicon="true" />
        </div>
    </div>
</template>
