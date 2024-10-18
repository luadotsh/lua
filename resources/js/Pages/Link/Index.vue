<script setup>
import { onMounted, ref } from "vue";
import { Link, useForm, Head } from "@inertiajs/vue3";
import debounce from "@/debounce";

import { PhSealPercent, PhMagnifyingGlass } from "@phosphor-icons/vue";
import date from "@/date";

import CreateModal from "./Create.vue";
import EmptyState from "@/Components/EmptyState.vue";
import AppLayout from "@/Layouts/Master.vue";
import Pagination from "@/Components/Pagination.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";

const createModal = ref(null);
const editModal = ref(null);

const searchForm = useForm({
    q: "",
});

defineProps({
    table: Object,
    hasData: Boolean,
});

const searchDebounce = debounce(function () {
    // faz o post
    searchForm.get(route("links.index"), {
        preserveScroll: true,
        preserveState: true,
    });
}, 300);

onMounted(() => {
    if (route().params.q) {
        searchForm.q = route().params.q;
    }
});
</script>

<template>
    <Head title="Links" />

    <CreateModal ref="createModal" />

    <AppLayout>
        <template #header>
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="page-title">
                            {{ `Links (${table.total})` }}
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

        <div class="px-4 sm:px-0">
            <div class="flex flex-col">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div
                        class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8"
                    >
                        <div class="table-wrapper">
                            <table class="table">
                                <thead class="table-thead">
                                    <tr class="table-tr">
                                        <th scope="col" class="table-th">
                                            Link
                                        </th>

                                        <th scope="col" class="table-th">To</th>
                                        <th scope="col" class="table-th">
                                            Clicks
                                        </th>

                                        <th scope="col" class="table-th">
                                            Last Click
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody">
                                    <tr
                                        as="tr"
                                        v-for="data in table.data"
                                        :key="data.id"
                                        class="table-tr"
                                    >
                                        <td class="table-td">
                                            {{ data.link }}
                                        </td>

                                        <td class="table-td">
                                            {{ data.url }}
                                        </td>

                                        <td class="table-td">
                                            {{ data.clicks }}
                                        </td>
                                        <td class="table-td">
                                            {{
                                                date.formatDateTime(
                                                    data.last_click
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template v-if="table.next_page_url" #pagination>
            <Pagination :data="table" />
        </template>

        <!-- <EmptyState
            v-if="!hasData"
            :icon="PhSealPercent"
            title="Create a discount code"
            description="Increase sales by offering discounts on products. Your discounts will be displayed here."
            buttonTitle="Create Discount"
            method="post"
        >
        </EmptyState> -->
    </AppLayout>
</template>
