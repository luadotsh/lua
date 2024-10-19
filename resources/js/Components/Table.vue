<script setup>
import country from "@/country";
import helper from "@/helper";
import { onMounted, ref, computed, watch } from "vue";

const total = ref(0);

const prop = defineProps({
    data: {
        type: Object,
        required: true,
    },
    favicon: {
        type: Boolean,
        default: false,
    },
    country: {
        type: Boolean,
        default: false,
    },

    capitalize: {
        type: Boolean,
        default: false,
    },

    uppercase: {
        type: Boolean,
        default: false,
    },
});

const formatName = (name) => {
    return prop.country ? country.getCountryName(name) : name;
};

watch(
    prop,
    async () => {
        total.value = helper.calcTotal(prop.data);
    },
    { deep: true, immediate: true }
);
</script>

<template>
    <div v-if="prop.data.length >= 1">
        <div class="space-y-2 relative">
            <div
                v-for="value in prop.data"
                :key="value"
                class="relative w-full group"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div
                            :class="`absolute top-0 left-0 h-full dark:bg-opacity-20 rounded-r-md bg-zinc-100 dark:bg-zinc-500`"
                            :style="`max-width: 85%; width: ${helper.calcPercentage(
                                total,
                                value.y
                            )}%`"
                        ></div>
                        <div class="flex items-center space-x-2 z-10 px-2 py-1">
                            <img
                                v-if="prop.favicon"
                                :src="
                                    route('websites.favicon', {
                                        url: encodeURI(value.x),
                                    })
                                "
                                class="w-4 h-4"
                                :alt="value.x"
                            />

                            <div v-if="prop.country">
                                {{ country.getCountryFlag(value.x) }}
                            </div>

                            <div
                                :class="{
                                    'text-zinc-800 dark:text-white text-sm font-medium': true,
                                    capitalize: prop.capitalize,
                                    uppercase: prop.uppercase,
                                }"
                            >
                                {{ formatName(value.x) }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between min-w-[3rem]">
                        <div
                            class="invisible group-hover:visible flex text-xs font-semibold text-zinc-500 dark:hover:text-zinc-300 mr-2"
                        >
                            {{ helper.calcPercentage(total, value.y) }}%
                        </div>
                        <div
                            class="text-sm font-semibold text-zinc-800 dark:text-white"
                        >
                            {{ helper.kFormatter(value.y) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        v-else
        class="min-h-[300px] flex flex-col sm:justify-center items-center"
    >
        <div class="text-zinc-800 dark:text-zinc-300 font-medium text-sm">
            No data
        </div>
    </div>
</template>
