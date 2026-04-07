<script setup lang="ts">
import { ref } from "vue";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import City from "./City.vue";
import Country from "./Country.vue";
import Region from "./Region.vue";

interface Range {
    timezone: string;
    group: string;
    start: string | null;
    end: string | null;
}

const { range } = defineProps<{
    range: Range;
}>();

const tab = ref("countries");
</script>

<template>
    <div class="min-h-[450px]">
        <Tabs v-model="tab">
            <div class="flex items-center justify-between mb-4">
                <div class="capitalize text-base font-semibold text-black dark:text-white">
                    Users
                </div>
                <TabsList>
                    <TabsTrigger value="countries">Countries</TabsTrigger>
                    <TabsTrigger value="regions">Regions</TabsTrigger>
                    <TabsTrigger value="cities">Cities</TabsTrigger>
                </TabsList>
            </div>

            <TabsContent value="countries">
                <Country :range="range" />
            </TabsContent>
            <TabsContent value="regions">
                <Region :range="range" />
            </TabsContent>
            <TabsContent value="cities">
                <City :range="range" />
            </TabsContent>
        </Tabs>
    </div>
</template>
