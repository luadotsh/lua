<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";

import { PhX, PhGear, PhGlobe } from "@phosphor-icons/vue";

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
                <div class="sm:flex sm:items-center">
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
            <div class="px-4 sm:px-0 space-y-2">
                <template v-for="domain in domains" :key="domain.id">
                    <div class="flex flex-1 items-center space-x-1">
                        <div
                            class="flex flex-1 items-center justify-between rounded-md px-4 py-2 border border-zinc-100 dark:border-zinc-700"
                        >
                            <div class="flex flex-1 items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="font-medium text-sm text-zinc-600 dark:text-white"
                                    >
                                        {{ domain.domain }}
                                    </div>
                                    <DomainStatus :domain="domain" />
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Button
                                    class="btn-secondary btn-xs space-x-1"
                                    @click="editModal.open(domain)"
                                >
                                    <PhGear class="h-3 w-3 stroke-2" />
                                    <div>Edit</div>
                                </Button>
                                <Button
                                    class="btn-secondary btn-xs space-x-1"
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
                                >
                                    <PhX class="h-3 w-3 stroke-2" />
                                    <div>Delete</div>
                                </Button>
                            </div>
                        </div>
                    </div>
                </template>
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
