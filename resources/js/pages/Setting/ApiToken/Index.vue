<script setup lang="ts">
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import ConfirmDeleteModal from "@/components/ConfirmDeleteModal.vue";
import { IconKey, IconTrash } from "@tabler/icons-vue";
import date from "@/date";
import EmptyState from "@/components/EmptyState.vue";
import AppLayout from "@/layouts/AppLayout.vue";
import CreateModal from "./Create.vue";
import * as apiTokensRoutes from "@/routes/setting/api-tokens";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import type { ApiToken } from "@/types";

const createModal = ref<InstanceType<typeof CreateModal> | null>(null);
const deleteModal = ref<InstanceType<typeof ConfirmDeleteModal> | null>(null);

defineProps<{
    tokens: ApiToken[];
    hasData: boolean;
}>();

// Anything still authenticating with this token stops working the moment it
// goes, so you type the token's own name.
const confirmDelete = (token: ApiToken) => {
    deleteModal.value?.open({
        url: apiTokensRoutes.destroy.url(token.id),
        // A token created without a name falls back to the generic keyword;
        // there is nothing specific to read back.
        confirmText: token.name ?? "delete",
    });
};

</script>

<template>
    <Head title="API Tokens" />

    <CreateModal ref="createModal" />

    <ConfirmDeleteModal
        ref="deleteModal"
        title="Delete API token"
        description="Anything still authenticating with this token stops working immediately."
    />

    <AppLayout title="API Tokens" :total="tokens.length" full-width>
        <template #header-actions>
            <Button @click="createModal?.open()">New API Token</Button>
        </template>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col">
            <div
                v-if="hasData"
                class="min-h-0 min-w-0 flex-1 overflow-auto pb-px"
                data-testid="api-tokens-scroll"
            >
                <Table>
                    <TableHeader sticky>
                        <TableRow>
                            <TableHead class="w-full">Name</TableHead>
                            <TableHead class="w-px whitespace-nowrap">Last used</TableHead>
                            <TableHead class="w-px whitespace-nowrap">Expires</TableHead>
                            <TableHead class="w-px">
                                <span class="sr-only">Actions</span>
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody id="api-tokens-body">
                        <TableRow v-for="token in tokens" :key="token.id">
                            <TableCell class="font-medium">{{ token.name }}</TableCell>

                            <TableCell class="w-px whitespace-nowrap text-muted-foreground">
                                {{
                                    token.last_used_at
                                        ? date.diffForHumans(token.last_used_at)
                                        : "Never"
                                }}
                            </TableCell>

                            <TableCell class="w-px whitespace-nowrap text-muted-foreground">
                                {{ token.expires_at ? date.formatDate(token.expires_at) : "—" }}
                            </TableCell>

                            <TableCell class="w-px">
                                <div class="flex items-center justify-end">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="size-8 text-muted-foreground hover:text-destructive"
                                                :data-testid="`api-token-delete-${token.id}`"
                                                @click="confirmDelete(token)"
                                            >
                                                <IconTrash class="size-3.5" />
                                                <span class="sr-only">Delete</span>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>Delete</TooltipContent>
                                    </Tooltip>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <EmptyState
                v-else
                :icon="IconKey"
                title="No API tokens yet"
                description="API tokens are required to use the API, so you can manage your links, tags and domains programmatically."
                class="p-4 sm:p-6"
            />
        </div>
    </AppLayout>
</template>
