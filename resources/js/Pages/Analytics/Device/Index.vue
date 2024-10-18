<script setup>
import { ref } from "vue";
import Tab from "@/Components/Tab.vue";
import Browser from "./Browser.vue";
import OS from "./Os.vue";
import Language from "./Language.vue";
import Device from "./Device.vue";

const tabs = ["devices", "browsers", "OS", "language"];
const tab = ref("devices");

const { dateRange } = defineProps({
    dateRange: Object,
});

const setTab = (value) => {
    tab.value = value;
};
</script>

<template>
    <div class="col-span-12 lg:col-span-6 card p-4 min-h-[450px]">
        <Tab :currentTab="tab" :tabs="tabs" @update="setTab" :title="tab" />

        <div class="mt-4">
            <Device
                v-if="tab == 'devices'"
                key="devices"
                :dateRange="dateRange"
            />
            <Browser
                v-else-if="tab == 'browsers'"
                key="browsers"
                :dateRange="dateRange"
            />
            <OS v-else-if="tab == 'OS'" key="OS" :dateRange="dateRange" />
            <Language
                v-else-if="tab == 'language'"
                key="language"
                :dateRange="dateRange"
            />
        </div>
    </div>
</template>
