<script setup lang="ts">
import { ref } from "vue";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import Browser from "./Browser.vue";
import OS from "./Os.vue";
import Language from "./Language.vue";
import Device from "./Device.vue";

interface Range {
    timezone: string;
    group: string;
    start: string | null;
    end: string | null;
}

const { range } = defineProps<{
    range: Range;
}>();

const tab = ref("devices");
</script>

<template>
    <div class="min-h-[450px]">
        <Tabs v-model="tab">
            <div class="flex items-center justify-between mb-4">
                <div class="capitalize text-base font-semibold text-black dark:text-white">
                    {{ tab }}
                </div>
                <TabsList>
                    <TabsTrigger value="devices">Devices</TabsTrigger>
                    <TabsTrigger value="browsers">Browsers</TabsTrigger>
                    <TabsTrigger value="OS">OS</TabsTrigger>
                    <TabsTrigger value="language">Language</TabsTrigger>
                </TabsList>
            </div>

            <TabsContent value="devices">
                <Device :range="range" />
            </TabsContent>
            <TabsContent value="browsers">
                <Browser :range="range" />
            </TabsContent>
            <TabsContent value="OS">
                <OS :range="range" />
            </TabsContent>
            <TabsContent value="language">
                <Language :range="range" />
            </TabsContent>
        </Tabs>
    </div>
</template>
