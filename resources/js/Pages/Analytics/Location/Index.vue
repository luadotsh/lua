<script setup>
import { ref, onMounted } from "vue";
import Tab from "@/Components/Tab.vue";
import City from "./City.vue";
import Country from "./Country.vue";
import Region from "./Region.vue";

const { range } = defineProps({
    range: Object,
});

const tab = ref("countries");
const tabs = ["countries", "regions", "cities"];

const setTab = (value) => {
    tab.value = value;
};
</script>

<template>
    <div class="min-h-[450px]">
        <Tab :currentTab="tab" :tabs="tabs" @update="setTab" title="Users" />
        <div class="mt-4">
            <Country v-if="tab == 'countries'" key="countries" :range="range" />
            <Region v-else-if="tab == 'regions'" key="regions" :range="range" />
            <City v-else-if="tab == 'cities'" key="cities" :range="range" />
        </div>
    </div>
</template>
