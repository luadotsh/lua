<script setup>
import { ref } from "vue";
import { PhCaretDown } from "@phosphor-icons/vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

import Tab from "@/Components/Tab.vue";
import Referer from "./Referer.vue";
import Medium from "./Medium.vue";
import Source from "./Source.vue";
import Campaign from "./Campaign.vue";
import Content from "./Content.vue";
import Term from "./Term.vue";

const { range } = defineProps({
    range: Object,
});

const tabNames = ["Referer"];
const tabs = { Referer, Medium, Source, Campaign, Content, Term };
const tab = ref("Referer");
const campaigns = [
    "Referer",
    "Medium",
    "Source",
    "Campaign",
    "Content",
    "Term",
];

const setTab = (value) => {
    tab.value = value;
};
</script>

<template>
    <div class="min-h-[450px]">
        <Tab
            :currentTab="tab"
            :tabs="tabNames"
            @update="setTab"
            title="Referers"
        >
            <template #left>
                <Menu as="div" class="relative inline-block text-left z-20">
                    <div>
                        <MenuButton
                            :class="{
                                'cursor-pointer flex items-center capitalize text-xs font-medium rounded-md px-2 py-1.5 border': true,
                                ' ': tab !== 'Referer',
                                'text-zinc-600 dark:text-zinc-400 border-transparent':
                                    tab === 'Referer',
                            }"
                        >
                            {{ tab == "Referer" ? "All " : tab }}
                            <PhCaretDown
                                class="h-4 w-4 ml-1"
                                aria-hidden="true"
                            />
                        </MenuButton>
                    </div>

                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <MenuItems
                            class="absolute right-0 z-10 mt-1 w-44 origin-top-right border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-zinc-700 rounded-md bg-white dark:bg-[#303030] shadow-lg focus:outline-none"
                        >
                            <div class="p-1">
                                <MenuItem
                                    v-for="campaign in campaigns"
                                    :key="campaign"
                                    v-slot="{ active }"
                                >
                                    <div
                                        @click="setTab(campaign)"
                                        class="text-gray-700 dark:text-white block px-4 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700"
                                    >
                                        {{ campaign }}
                                    </div>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </template>
        </Tab>
        <div class="mt-4">
            <component :is="tabs[tab]" :range="range" />
        </div>
    </div>
</template>
