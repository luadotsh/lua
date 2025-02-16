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
                <Menu as="div" class="relative z-20 inline-block text-left">
                    <div>
                        <MenuButton
                            :class="{
                                'cursor-pointer flex items-center capitalize text-xs font-medium rounded-md px-3 py-1.5 border': true,
                                'border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-300':
                                    tab !== 'Referer',
                                'text-zinc-600 dark:text-zinc-400 border-transparent':
                                    tab === 'Referer',
                            }"
                        >
                            {{ tab == "Referer" ? "All " : tab }}
                            <PhCaretDown
                                class="w-4 h-4 ml-1"
                                aria-hidden="true"
                            />
                        </MenuButton>
                    </div>

                    <transition
                        enter-active-class="transition duration-100 ease-out"
                        enter-from-class="transform scale-95 opacity-0"
                        enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0"
                    >
                        <MenuItems
                            class="absolute right-0 z-10 mt-2 origin-top-right bg-white divide-y rounded-md shadow-lg w-36 divide-zinc-100 dark:divide-zinc-700 dark:bg-zinc-900 ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <div class="p-1">
                                <MenuItem
                                    v-for="campaign in campaigns"
                                    :key="campaign"
                                    v-slot="{ active }"
                                >
                                    <div
                                        @click="setTab(campaign)"
                                        :class="[
                                            active
                                                ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300'
                                                : 'text-zinc-800 dark:text-zinc-300',
                                            ' px-4 py-1.5 text-sm cursor-pointer flex flex-1 items-center space-x-2 rounded',
                                        ]"
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
