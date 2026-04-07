<script setup lang="ts">
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { IconX, IconKey } from "@tabler/icons-vue";
import date from "@/date";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import CreateModal from "./Create.vue";
import * as apiTokensRoutes from "@/routes/setting/api-tokens";

const createModal = ref<InstanceType<typeof CreateModal> | null>(null);
const tokenToDelete = ref<{ id: string | number } | null>(null);

defineProps<{
    tokens: object;
    hasData: boolean;
}>();

const confirmDelete = (token: { id: string | number }) => {
    tokenToDelete.value = token;
};

const deleteToken = () => {
    if (!tokenToDelete.value) {
        return;
    }

    router.delete(apiTokensRoutes.destroy.url(tokenToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            tokenToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="API Tokens" />

    <AppLayout>
        <SettingsLayout>
            <CreateModal ref="createModal" />

            <AlertDialog :open="!!tokenToDelete" @update:open="(val) => !val && (tokenToDelete = null)">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Delete API Token</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to delete this api token?
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="tokenToDelete = null">Cancel</AlertDialogCancel>
                        <AlertDialogAction @click="deleteToken" class="bg-red-600 hover:bg-red-700">Delete</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold">API Tokens</h2>
                <Button @click="createModal?.open()">
                    New API Token
                </Button>
            </div>

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
                                                ? `Last used ${date.diffForHumans(token.last_used_at)}`
                                                : "Never used"
                                        }}
                                    </div>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="space-x-1"
                                        @click="confirmDelete(token)"
                                    >
                                        <IconX class="h-3 w-3" />
                                        <span>Delete</span>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div
                v-if="!hasData"
                class="flex flex-col items-center justify-center py-12 text-center"
            >
                <IconKey class="h-12 w-12 text-zinc-300 mb-4" />
                <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">You don't have any API Tokens yet.</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">API Tokens are required to use the API, so you can manage your links, tags, and domains programmatically.</p>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
