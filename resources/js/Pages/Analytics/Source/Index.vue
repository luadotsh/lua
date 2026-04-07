<script setup lang="ts">
import { ref } from "vue";
import { IconChevronDown } from "@tabler/icons-vue";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import Referer from "./Referer.vue";
import Medium from "./Medium.vue";
import Source from "./Source.vue";
import Campaign from "./Campaign.vue";
import Content from "./Content.vue";
import Term from "./Term.vue";

interface Range {
    timezone: string;
    group: string;
    start: string | null;
    end: string | null;
}

const { range } = defineProps<{
    range: Range;
}>();

const tabs: Record<string, unknown> = { Referer, Medium, Source, Campaign, Content, Term };
const tab = ref("Referer");
const campaigns = ["Referer", "Medium", "Source", "Campaign", "Content", "Term"];

const setTab = (value: string) => {
    tab.value = value;
};
</script>

<template>
    <div class="min-h-[450px]">
        <Tabs v-model="tab">
            <div class="flex items-center justify-between mb-4">
                <div class="capitalize text-base font-semibold text-black dark:text-white">
                    Referers
                </div>
                <div class="flex items-center space-x-2">
                    <DropdownMenu>
                        <DropdownMenuTrigger
                            :class="{
                                'cursor-pointer flex items-center capitalize text-xs font-medium rounded-md px-3 py-1.5 border': true,
                                'border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-300':
                                    tab !== 'Referer',
                                'text-zinc-600 dark:text-zinc-400 border-transparent':
                                    tab === 'Referer',
                            }"
                        >
                            {{ tab === "Referer" ? "All " : tab }}
                            <IconChevronDown
                                class="w-4 h-4 ml-1"
                                aria-hidden="true"
                            />
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-36">
                            <DropdownMenuItem
                                v-for="campaign in campaigns"
                                :key="campaign"
                                @click="setTab(campaign)"
                            >
                                {{ campaign }}
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <TabsList>
                        <TabsTrigger value="Referer">Referer</TabsTrigger>
                    </TabsList>
                </div>
            </div>

            <TabsContent value="Referer">
                <component :is="tabs[tab]" :range="range" />
            </TabsContent>
        </Tabs>
    </div>
</template>
