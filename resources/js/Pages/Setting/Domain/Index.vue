<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

import {
    PhGlobe,
    PhPencil,
    PhDotsThreeOutlineVertical,
    PhArrowBendDownRight,
    PhTrash,
} from "@phosphor-icons/vue";

import DomainStatus from "@/Components/DomainStatus.vue";
import EmptyState from "@/Components/EmptyState.vue";
import AppLayout from "@/Layouts/Master.vue";
import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";

const confirmDeleteModal = ref(null);
const createModal = ref(null);
const editModal = ref(null);

const { domains, hasData } = defineProps({
    domains: Object,
    hasData: Boolean,
});
</script>

<template>
    <Head title="Tags" />

    <AppLayout>
        <CreateModal ref="createModal" />
        <EditModal ref="editModal" />

        <ConfirmDeleteModal
            ref="confirmDeleteModal"
            description="Are you sure you want to delete this domain?"
        />

        <template #header>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <div class="sm:flex-auto">
                        <h1 class="page-title">Domains</h1>
                    </div>
                    <div>
                        <Button class="btn-primary" @click="createModal.open()">
                            New Domain
                        </Button>
                    </div>
                </div>
            </div>
        </template>

        <div>
            <div
                class="w-full flex flex-col transition-[gap,opacity] min-w-0 gap-4"
            >
                <div
                    v-for="domain in domains"
                    :key="domain.id"
                    class="flex items-center justify-between group border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 border rounded-lg p-2 lg:p-4"
                >
                    <div class="flex items-center justify-center lg:space-x-4">
                        <div
                            class="rounded-full hidden lg:flex border border-zinc-200 dark:border-zinc-700 dark:bg-white/5 p-0.5"
                        >
                            <img
                                :src="
                                    route('websites.favicon', {
                                        url: domain.domain,
                                    })
                                "
                                alt="favicon"
                                class="h-8 w-8 rounded-full"
                            />
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <div class="text-zinc-800 dark:text-zinc-300">
                                    {{ domain.domain }}
                                </div>
                            </div>
                            <div class="ml-0.5 flex items-center space-x-2">
                                <PhArrowBendDownRight
                                    weight="bold"
                                    class="text-zinc-400 h-4 w-4"
                                />
                                <div
                                    class="text-[13px] text-zinc-600 dark:text-zinc-400"
                                >
                                    {{
                                        domain.not_found_url ??
                                        "No redirect configured"
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <DomainStatus :domain="domain" />

                        <Menu as="div" class="relative inline-block text-left">
                            <div>
                                <MenuButton class="flex items-center">
                                    <PhDotsThreeOutlineVertical
                                        class="h-5 w-5 text-gray-400"
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
                                    class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                >
                                    <div class="py-1">
                                        <MenuItem v-slot="{ active }">
                                            <div
                                                @click="editModal.open(domain)"
                                                :class="[
                                                    active
                                                        ? 'bg-gray-100 text-gray-900'
                                                        : 'text-gray-700',
                                                    'group flex items-center px-4 py-2 text-sm',
                                                ]"
                                            >
                                                <PhPencil
                                                    class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                                    aria-hidden="true"
                                                />
                                                Edit
                                            </div>
                                        </MenuItem>
                                    </div>

                                    <div class="py-1">
                                        <MenuItem v-slot="{ active }">
                                            <div
                                                @click="
                                                    confirmDeleteModal.open({
                                                        url: route(
                                                            'setting.domains.destroy',
                                                            {
                                                                id: domain.id,
                                                            }
                                                        ),
                                                    })
                                                "
                                                :class="[
                                                    active
                                                        ? 'bg-red-50 text-red-600'
                                                        : 'text-red-500',
                                                    'group flex items-center px-4 py-2 text-sm cursor-pointer',
                                                ]"
                                            >
                                                <PhTrash
                                                    class="mr-3 h-5 w-5 text-red-500"
                                                    aria-hidden="true"
                                                />
                                                Delete
                                            </div>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                </div>
            </div>
        </div>

        <EmptyState
            v-if="!hasData"
            :icon="PhGlobe"
            title="You don't have any domains yet."
            description="Domains are used to create branded short links. e.g. link.yourdomain.com/short-link"
            buttonTitle="Add Domain"
        >
        </EmptyState>
    </AppLayout>
</template>
