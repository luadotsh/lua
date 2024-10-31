<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import { PhX, PhKey } from "@phosphor-icons/vue";
import date from "@/date";

import AppLayout from "@/Layouts/Master.vue";
import CreateModal from "./Create.vue";
import EmptyState from "@/Components/EmptyState.vue";

const confirmDeleteModal = ref(null);
const createModal = ref(null);

const { tokens, hasData } = defineProps({
    tokens: Object,
    hasData: Boolean,
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
                            class="flex flex-1 items-center justify-between rounded-md px-4 py-2 border border-zinc-100 dark:border-zinc-700"
                        >
                            <div class="flex flex-1 items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="font-medium text-sm text-zinc-600 dark:text-white"
                                    >
                                        {{ token.name }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div
                                    class="text-sm text-zinc-800 dark:text-zinc-300"
                                >
                                    {{
                                        token.last_used_at
                                            ? `Last used ${date.diffForHumans(
                                                  token.last_used_at
                                              )}`
                                            : "Never used"
                                    }}
                                </div>
                                <Button
                                    class="btn-secondary btn-sm space-x-1"
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
        <EmptyState
            v-if="!hasData"
            :icon="PhKey"
            title="You don't have any API Tokens yet."
            description="API Tokens are required to use the API, so you can manage your links, tags, and domains programmatically."
        >
        </EmptyState>
    </AppLayout>
</template>
