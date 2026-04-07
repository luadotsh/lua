<script setup lang="ts">
import { ref, watch } from "vue";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import Table from "@/components/Table.vue";
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

const tab = ref("links");
const data = ref(null);

const loadData = () => {
    axios
        .get(statistics.url({ query: { ...props.range, metric: "links" } }))
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
    <div class="min-h-[450px]">
        <Tabs v-model="tab">
            <div class="flex items-center justify-between mb-4">
                <div class="capitalize text-base font-semibold text-black dark:text-white">
                    Links
                </div>
                <TabsList>
                    <TabsTrigger value="links">Links</TabsTrigger>
                </TabsList>
            </div>

            <TabsContent value="links">
                <Table v-if="data" :data="data" :favicon="true" />
            </TabsContent>
        </Tabs>
    </div>
</template>
