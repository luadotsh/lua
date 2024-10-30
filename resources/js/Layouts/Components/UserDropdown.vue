<script setup>
import { usePage, Link, router } from "@inertiajs/vue3";

import {
    PhCaretDown,
    PhCaretUp,
    PhUser,
    PhShoppingBag,
    PhQuestion,
    PhPower,
    PhPlus,
    PhCheck,
    PhBookOpen,
    PhStorefront,
} from "@phosphor-icons/vue";

import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

import UserAvatar from "@/Components/UserAvatar.vue";

const user = usePage().props.auth.user;
const workspaces = user.workspaces;

const switchToWorkspace = (workspace) => {
    router.put(
        route("workspaces.update-current"),
        {
            workspace_id: workspace.id,
        },
        {
            preserveState: false,
        }
    );
};
</script>
<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton
                v-slot="{ open }"
                class="inline-flex w-full justify-between gap-x-1.5 rounded -mx-2 py-1.5 px-3 text-sm font-medim text-zinc-900 hover:bg-zinc-200 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
            >
                <div class="flex items-center space-x-2">
                    <UserAvatar :user="user" size="sm" />

                    <div class="text-left truncate font-medium">
                        {{ user.name }}
                    </div>
                </div>

                <PhCaretUp
                    v-if="open"
                    class="h-5 w-5 text-zinc-400"
                    weight="bold"
                />
                <PhCaretDown
                    v-else
                    class="h-5 w-5 text-zinc-400"
                    weight="bold"
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
                class="absolute top-0 -left-2 z-10 mt-10 w-full lg:w-52 border border-zinc-200 dark:border-zinc-700 origin-bottom-right divide-y divide-zinc-100 dark:divide-zinc-700 rounded bg-white dark:bg-zinc-800 shadow-lg focus:outline-none"
            >
                <div class="py-1">
                    <MenuItem
                        v-for="workspace in workspaces"
                        :key="workspace"
                        v-slot="{ active }"
                    >
                        <div
                            @click="switchToWorkspace(workspace)"
                            :class="[
                                active
                                    ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white'
                                    : 'text-zinc-500 dark:text-zinc-400',
                                'cursor-pointer px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
                            ]"
                        >
                            <div
                                class="flex flex-grow items-center space-x-2 overflow-hidden"
                            >
                                <img
                                    :src="workspace.logo_url"
                                    class="w-5 h-5 rounded"
                                    :alt="workspace.name"
                                />
                                <div
                                    class="flex flex-1 items-center justify-between overflow-hidden space-x-2"
                                >
                                    <div class="truncate">
                                        {{ workspace.name }}
                                    </div>
                                    <PhCheck
                                        v-if="
                                            workspace.id ==
                                            user.current_workspace.id
                                        "
                                        class="h-4 w-4 text-green-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <Link
                            :href="route('workspaces.create')"
                            :class="[
                                active
                                    ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white'
                                    : 'text-zinc-500 dark:text-zinc-400',
                                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
                            ]"
                        >
                            <PhPlus class="w-5 h-5" />
                            <div>New Workspace</div>
                        </Link>
                    </MenuItem>
                </div>
                <div class="py-1">
                    <MenuItem v-slot="{ active }">
                        <a
                            href="https://developers.lua.sh"
                            target="_blank"
                            :class="[
                                active
                                    ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white'
                                    : 'text-zinc-500 dark:text-zinc-400',
                                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
                            ]"
                        >
                            <PhQuestion class="w-5 h-5" />
                            <div>Help Center</div>
                        </a>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <a
                            href="https://developers.lua.sh"
                            target="_blank"
                            :class="[
                                active
                                    ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white'
                                    : 'text-zinc-500 dark:text-zinc-400',
                                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
                            ]"
                        >
                            <PhBookOpen class="w-5 h-5" />
                            <div>API Reference</div>
                        </a>
                    </MenuItem>
                </div>
                <div class="py-1">
                    <MenuItem v-slot="{ active }">
                        <Link
                            :href="route('setting.account.edit')"
                            :class="[
                                active
                                    ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white'
                                    : 'text-zinc-500 dark:text-zinc-400',
                                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
                            ]"
                        >
                            <PhUser class="w-5 h-5" />
                            <div>My Account</div>
                        </Link>
                    </MenuItem>
                </div>

                <div class="py-1">
                    <MenuItem v-slot="{ active }">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            :class="[
                                active
                                    ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-white'
                                    : 'text-zinc-500 dark:text-zinc-400',
                                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
                            ]"
                        >
                            <PhPower class="w-5 h-5" />
                            <div>Sign Out</div>
                        </Link>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
