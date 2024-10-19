<script setup>
import { ref, onMounted, watch } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import Button from "@/Components/Button.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";

import { PhX, PhGear, PhDotsSixVertical } from "@phosphor-icons/vue";

import AppLayout from "@/Layouts/Master.vue";
import CreateModal from "./Create.vue";

const confirmDeleteModal = ref(null);
const createModal = ref(null);

const { tokens } = defineProps({
    tokens: Object,
});
</script>

<template>
    <Head title="Tags" />

    <AppLayout>
        <CreateModal ref="createModal" />

        <ConfirmDeleteModal
            ref="confirmDeleteModal"
            description="Are you sure you want to delete this api token?"
        />

        <template #header>
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="page-title">API Tokens</h1>
                    </div>
                    <div>
                        <Button class="btn-primary" @click="createModal.open()">
                            New API Token
                        </Button>
                    </div>
                </div>
            </div>
        </template>

        <div>
            <div class="px-4 sm:px-0 space-y-2">
                <template v-for="token in tokens" :key="token.id">
                    <div class="flex flex-1 items-center space-x-1">
                        <div
                            class="flex flex-1 items-center justify-between rounded-md px-4 py-2 border border-gray-100 dark:border-gray-700"
                        >
                            <div class="flex flex-1 items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="font-medium text-sm text-gray-600 dark:text-white"
                                    >
                                        {{ token.name }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Button
                                    class="btn-secondary btn-xs space-x-1"
                                    @click="
                                        confirmDeleteModal.open({
                                            url: route(
                                                'setting.api-tokens.destroy',
                                                {
                                                    id: token.id,
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
    </AppLayout>
</template>
