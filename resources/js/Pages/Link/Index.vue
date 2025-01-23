<script setup>
import { onMounted, ref, computed } from "vue";
import { Link, useForm, Head } from "@inertiajs/vue3";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import helper from "@/helper";
import date from "@/date";

import debounce from "@/debounce";

import {
    PhSealPercent,
    PhMagnifyingGlass,
    PhCopy,
    PhCursorClick,
    PhPencil,
    PhDotsThreeOutlineVertical,
    PhArrowBendDownRight,
    PhTrash,
    PhQrCode,
} from "@phosphor-icons/vue";

import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import EmptyState from "@/Components/EmptyState.vue";
import AppLayout from "@/Layouts/Master.vue";
import Pagination from "@/Components/Pagination.vue";
import Tag from "@/Components/Tag.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import Qrcode from "@/Components/Qrcode.vue";

import CreateModal from "./Create.vue";
import Edit from "./Edit.vue";

const qrcodeModal = ref(null);
const createModal = ref(null);
const confirmDeleteModal = ref(null);

const searchForm = useForm({
    q: "",
});

const { table, hasData, link } = defineProps({
    table: Object,
    hasData: Boolean,
    link: {
        type: Object,
        default: null,
    },
});

const searchDebounce = debounce(function () {
    // faz o post
    searchForm.get(route("links.index"), {
        preserveScroll: true,
        preserveState: true,
    });
}, 300);

const title = computed(() => {
    if (searchForm.q) {
        return `Search results for "${searchForm.q}"`;
    }

    if (table.total <= 1) {
        return `Links`;
    }

    return `Links (${table.total})`;
});

const formatLastClick = (data) => {
    if (!data.last_click) {
        return "No clicks yet";
    }

    return `
        <div class="text-xs text-white">
            Last Click was ${date.diffForHumans(data.last_click)}
        </div>
    `;
};

onMounted(() => {
    if (route().params.q) {
        searchForm.q = route().params.q;
    }
});
</script>

<template>
    <Head title="Links" />

    <CreateModal ref="createModal" />
    <Edit v-if="link" :link="link" />

    <Qrcode ref="qrcodeModal" />

    <AppLayout>
        <template #header>
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="page-title">
                            {{ title }}
                        </h1>
                    </div>
                    <div
                        class="mt-4 sm:mt-0 sm:ml-16 flex space-x-2 sm:flex-none"
                    >
                        <div class="relative flex-1">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <PhMagnifyingGlass
                                    class="h-5 w-5 text-zinc-400"
                                />
                            </div>
                            <Input
                                class="w-full !pl-10"
                                id="search-field"
                                autocomplete="off"
                                type="text"
                                placeholder="Search..."
                                v-model="searchForm.q"
                                @keyup="searchDebounce"
                            />
                        </div>

                        <Button
                            class="btn btn-primary"
                            @click="createModal.open()"
                        >
                            New Link
                        </Button>
                    </div>
                </div>
            </div>
        </template>

        <ConfirmDeleteModal
            ref="confirmDeleteModal"
            description="Are you sure you want to delete this link?"
        />

        <div>
            <div
                class="w-full flex flex-col transition-[gap,opacity] min-w-0 space-y-4"
            >
                <div
                    v-for="data in table.data"
                    :key="data.id"
                    class="flex items-center lg:justify-between group border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 border rounded-lg p-2.5 lg:p-4"
                >
                    <div class="min-w-0 grow">
                        <div
                            class="flex items-start lg:items-center lg:space-x-4"
                        >
                            <div
                                class="hidden lg:flex flex-none rounded-full border border-zinc-200 dark:border-zinc-700 dark:bg-white/5 p-0.5"
                            >
                                <img
                                    :src="
                                        route('websites.favicon', {
                                            url: data.url,
                                        })
                                    "
                                    alt="favicon"
                                    class="h-6 w-6 lg:h-8 lg:w-8 rounded-full"
                                />
                            </div>
                            <div class="overflow-hidden">
                                <div class="flex items-center space-x-2 mb-1">
                                    <div
                                        class="text-zinc-800 dark:text-zinc-300 text-sm lg:text-base truncate"
                                    >
                                        {{ data.link }}
                                    </div>
                                    <PhCopy
                                        class="h-4 lg:h-5 w-4 lg:w-5 text-gray-400 cursor-pointer"
                                        @click="
                                            helper.copyToClipboard(
                                                data.link,
                                                'Link copied'
                                            )
                                        "
                                    />
                                </div>
                                <div class="ml-0.5 flex items-center space-x-2">
                                    <PhArrowBendDownRight
                                        weight="bold"
                                        class="text-zinc-400 w-4 h-4"
                                    />
                                    <div
                                        class="text-[13px] text-zinc-600 dark:text-zinc-400 truncate"
                                    >
                                        {{ data.url }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-none flex items-center space-x-4">
                        <div class="hidden lg:flex items-center space-x-1.5">
                            <div v-for="tag in data.tags" :key="tag.id">
                                <Tag :tag="tag" />
                            </div>
                        </div>
                        <div
                            v-tooltip="{
                                content: formatLastClick(data),
                                placement: 'top',
                                html: true,
                            }"
                            class="inline-flex items-center ring-1 ring-inset ring-opacity-40 text-sm truncate font-medium px-2 py-1 rounded bg-zinc-100 dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 ring-zinc-300 dark:ring-zinc-700 space-x-1 p-4"
                        >
                            <PhCursorClick class="h-3 lg:h-4 w-3 lg:w-4" />
                            <div class="flex items-center space-x-1">
                                <span>{{
                                    helper.kFormatter(data.clicks)
                                }}</span>
                                <span class="hidden lg:block">clicks</span>
                            </div>
                        </div>
                        <div>
                            <Menu
                                as="div"
                                class="relative inline-block text-left"
                            >
                                <div>
                                    <MenuButton>
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
                                        class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 dark:divide-gray-700 rounded-md bg-white dark:bg-zinc-900 shadow-lg ring-1 ring-black dark:ring-zinc-700 ring-opacity-5 focus:outline-none"
                                    >
                                        <div class="py-1">
                                            <MenuItem v-slot="{ active }">
                                                <Link
                                                    :href="
                                                        route(
                                                            'links.index',
                                                            data.id
                                                        )
                                                    "
                                                    :class="[
                                                        active
                                                            ? 'bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-300'
                                                            : 'text-gray-700 dark:text-zinc-300',
                                                        'flex items-center px-4 py-2 text-sm cursor-pointer',
                                                    ]"
                                                >
                                                    <PhPencil
                                                        class="mr-3 h-5 w-5 text-gray-400 dark:text-zinc-300"
                                                        aria-hidden="true"
                                                    />
                                                    Edit
                                                </Link>
                                            </MenuItem>
                                            <MenuItem v-slot="{ active }">
                                                <div
                                                    @click="
                                                        qrcodeModal.open(data)
                                                    "
                                                    :class="[
                                                        active
                                                            ? 'bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-300'
                                                            : 'text-gray-700 dark:text-zinc-300',
                                                        'flex items-center px-4 py-2 text-sm cursor-pointer',
                                                    ]"
                                                >
                                                    <PhQrCode
                                                        class="mr-3 h-5 w-5 text-gray-400 dark:text-zinc-300"
                                                        aria-hidden="true"
                                                    />
                                                    QR Code
                                                </div>
                                            </MenuItem>
                                        </div>
                                        <div class="py-1">
                                            <MenuItem v-slot="{ active }">
                                                <div
                                                    @click="
                                                        helper.copyToClipboard(
                                                            data.id,
                                                            'Link ID copied'
                                                        )
                                                    "
                                                    :class="[
                                                        active
                                                            ? 'bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-zinc-300'
                                                            : 'text-gray-700 dark:text-zinc-300',
                                                        'flex items-center px-4 py-2 text-sm cursor-pointer',
                                                    ]"
                                                >
                                                    <PhCopy
                                                        class="mr-3 h-5 w-5 text-gray-400 dark:text-zinc-300"
                                                        aria-hidden="true"
                                                    />
                                                    Copy Link ID
                                                </div>
                                            </MenuItem>
                                        </div>

                                        <div class="py-1">
                                            <MenuItem v-slot="{ active }">
                                                <div
                                                    @click="
                                                        confirmDeleteModal.open(
                                                            {
                                                                url: route(
                                                                    'links.destroy',
                                                                    {
                                                                        id: data.id,
                                                                    }
                                                                ),
                                                            }
                                                        )
                                                    "
                                                    :class="[
                                                        active
                                                            ? 'bg-zinc-100 dark:bg-zinc-800 text-red-600'
                                                            : 'text-red-500',
                                                        'flex items-center px-4 py-2 text-sm cursor-pointer',
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
        </div>

        <template v-if="table.next_page_url" #pagination>
            <Pagination :data="table" />
        </template>

        <EmptyState
            v-if="!hasData"
            :icon="PhSealPercent"
            title="You don't have any links yet"
            description="Create a new link and start tracking your clicks"
        >
        </EmptyState>
    </AppLayout>
</template>
