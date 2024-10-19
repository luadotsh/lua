<script setup>
import { ref } from "vue";
import Tab from "@/Components/Tab.vue";
import Browser from "./Browser.vue";
import OS from "./Os.vue";
import Language from "./Language.vue";
import Device from "./Device.vue";

const tabs = ["devices", "browsers", "OS", "language"];
const tab = ref("devices");

const { range } = defineProps({
    range: Object,
});

const setTab = (value) => {
    tab.value = value;
};
</script>

<template>
    <div class="min-h-[450px]">
        <Tab :currentTab="tab" :tabs="tabs" @update="setTab" :title="tab" />

        <div class="mt-4">
            <Device v-if="tab == 'devices'" key="devices" :range="range" />
            <Browser
                v-else-if="tab == 'browsers'"
                key="browsers"
                :range="range"
            />
            <OS v-else-if="tab == 'OS'" key="OS" :range="range" />
            <Language
                v-else-if="tab == 'language'"
                key="language"
                :range="range"
            />
        </div>
    </div>
</template>
