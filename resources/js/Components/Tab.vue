<script setup>
import { ref } from "vue";

const emit = defineEmits(["update"]);

const { tabs } = defineProps({
    tabs: Array,
    title: String,
    currentTab: String,
});

const setCurrentTab = (tab) => {
    emit("update", tab);
};
</script>

<template>
    <div class="flex items-center justify-between">
        <div
            class="capitalize text-base font-semibold text-black dark:text-white"
        >
            {{ title }}
        </div>

        <div class="flex items-center">
            <template v-if="tabs.length >= 2">
                <div
                    v-for="tab in tabs"
                    :key="tab"
                    @click="setCurrentTab(tab)"
                    :class="{
                        'cursor-pointer capitalize text-xs font-medium rounded-md px-2 py-1 border': true,
                        'text-black dark:text-white card': currentTab == tab,
                        'text-zinc-600 dark:text-zinc-400 border-transparent':
                            currentTab != tab,
                    }"
                >
                    {{ tab }}
                </div>
            </template>

            <slot name="right" />
        </div>
    </div>
</template>
