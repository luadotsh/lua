<script setup>
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import axios from "axios";

import {
    PhCaretDown,
    PhCaretUp,
    PhLink,
    PhChartLineUp,
    PhCursorClick,
    PhGear,
} from "@phosphor-icons/vue";

const navigation = [
    {
        name: "Analytics",
        href: route("analytics.index"),
        icon: PhChartLineUp,
        current: route().current("analytics.*"),
    },
    {
        name: "Links",
        href: route("links.index"),
        icon: PhLink,
        current: route().current("links.*"),
    },

    {
        name: "Events",
        href: route("events.index"),
        icon: PhCursorClick,
        current: route().current("events.*"),
    },
    {
        name: "Settings",
        href: route("setting.workspace.edit"),
        icon: PhGear,
        current: route().current("setting.*"),

        items: [
            {
                name: "Workspace",
                href: route("setting.workspace.edit"),
                current: route().current("setting.workspace.*"),
            },
            {
                name: "Domains",
                href: route("setting.domains.index"),
                current: route().current("setting.domains.*"),
            },

            {
                name: "API Tokens",
                href: route("setting.api-tokens.index"),
                current: route().current("setting.api-tokens.*"),
            },

            {
                name: "Tags",
                href: route("setting.tags.index"),
                current: route().current("setting.tags.*"),
            },
            {
                name: "Users",
                href: route("setting.team-members.index"),
                current: route().current("setting.team-members.*"),
            },
            {
                name: "Billing",
                href: route("setting.billing.index"),
                current: route().current("setting.billing.*"),
            },
        ],
    },
];
</script>

<template>
    <nav class="flex-1 flex flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-6">
            <li>
                <ul role="list" class="-mx-2 space-y-0.5">
                    <template v-for="item in navigation" :key="item.name">
                        <Link
                            :href="item.href"
                            as="li"
                            :class="[
                                item.current
                                    ? 'bg-zinc-800 dark:bg-zinc-800 text-white'
                                    : 'text-zinc-900 hover:bg-zinc-200 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white',
                                'group flex gap-x-3 rounded py-1.5 px-3 text-sm font-medium cursor-pointer',
                            ]"
                        >
                            <component :is="item.icon" class="h-5 w-5" />
                            <div
                                class="flex flex-1 items-center justify-between"
                            >
                                <div>
                                    {{ item.name }}
                                </div>
                                <div v-if="item.items">
                                    <PhCaretUp
                                        v-if="item.current"
                                        class="h-5 w-5 stroke-2"
                                    />
                                    <PhCaretDown
                                        v-else
                                        class="h-5 w-5 stroke-2"
                                    />
                                </div>
                            </div>
                        </Link>

                        <div v-if="item.items && item.current" class="py-1">
                            <div class="">
                                <ul v-for="item in item.items" :key="item">
                                    <Link
                                        :href="item.href"
                                        as="li"
                                        preserve-scroll
                                        preserve-state
                                        :class="[
                                            'pt-0.5 ml-5 -0 border-l border-zinc-300 dark:border-zinc-700',
                                        ]"
                                    >
                                        <div
                                            :class="[
                                                item.current
                                                    ? 'bg-zinc-800 dark:bg-zinc-800 text-white'
                                                    : 'text-zinc-900 hover:bg-zinc-200 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white',
                                                'ml-4 group flex gap-x-2 rounded py-1.5 px-1.5 text-sm font-medium cursor-pointer',
                                            ]"
                                        >
                                            <div></div>
                                            <div
                                                class="flex flex-1 items-center justify-between"
                                            >
                                                <div>{{ item.name }}</div>
                                            </div>
                                        </div>
                                    </Link>
                                </ul>
                            </div>
                        </div>
                    </template>
                </ul>
            </li>
        </ul>
    </nav>
</template>
