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
import { IconPencil, IconTrash, IconWorld } from "@tabler/icons-vue";
import DomainStatus from "@/components/DomainStatus.vue";
import EmptyState from "@/components/EmptyState.vue";
import AppLayout from "@/layouts/AppLayout.vue";
import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";
import * as domainsRoutes from "@/routes/setting/domains";
import * as websitesRoutes from "@/routes/websites";
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
import type { Domain } from "@/types";

const createModal = ref<InstanceType<typeof CreateModal> | null>(null);
const editModal = ref<InstanceType<typeof EditModal> | null>(null);
const domainToDelete = ref<{ id: string | number } | null>(null);

const props = defineProps<{
    domains: Domain[];
    hasData: boolean;
}>();

const confirmDelete = (domain: { id: string | number }) => {
    domainToDelete.value = domain;
};

const deleteDomain = () => {
    if (!domainToDelete.value) {
        return;
    }

    router.delete(domainsRoutes.destroy.url(domainToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            domainToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Domains" />

    <CreateModal ref="createModal" />
    <EditModal ref="editModal" />

    <AlertDialog
        :open="!!domainToDelete"
        @update:open="(open) => !open && (domainToDelete = null)"
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete domain</AlertDialogTitle>
                <AlertDialogDescription>
                    Short links already created on this domain stop resolving.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogAction
                    class="bg-destructive text-white hover:bg-destructive/90"
                    @click="deleteDomain"
                >
                    Delete
                </AlertDialogAction>
                <AlertDialogCancel @click="domainToDelete = null">Cancel</AlertDialogCancel>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>

    <AppLayout title="Domains" :total="domains.length" full-width>
        <template #header-actions>
            <Button @click="createModal?.open()">New Domain</Button>
        </template>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col">
            <div
                v-if="hasData"
                class="min-h-0 min-w-0 flex-1 overflow-auto pb-px"
                data-testid="domains-scroll"
            >
                <Table>
                    <TableHeader sticky>
                        <TableRow>
                            <TableHead class="w-full">Domain</TableHead>
                            <TableHead class="w-px whitespace-nowrap">
                                Not found redirect
                            </TableHead>
                            <TableHead class="w-px whitespace-nowrap">Status</TableHead>
                            <TableHead class="w-px">
                                <span class="sr-only">Actions</span>
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody id="domains-body">
                        <TableRow v-for="domain in domains" :key="domain.id">
                            <TableCell>
                                <span class="flex items-center gap-2">
                                    <img
                                        :src="websitesRoutes.favicon.url({ query: { url: domain.domain } })"
                                        alt=""
                                        aria-hidden="true"
                                        class="size-4 shrink-0 rounded-sm"
                                        loading="lazy"
                                        @error="(event) => ((event.target as HTMLImageElement).style.display = 'none')"
                                    />
                                    <span class="font-medium">{{ domain.domain }}</span>
                                </span>
                            </TableCell>

                            <TableCell class="w-px whitespace-nowrap text-muted-foreground">
                                {{ domain.not_found_url ?? "—" }}
                            </TableCell>

                            <TableCell class="w-px">
                                <DomainStatus :domain="domain" />
                            </TableCell>

                            <TableCell class="w-px">
                                <div class="flex items-center justify-end gap-1">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="size-8"
                                                :data-testid="`domain-edit-${domain.id}`"
                                                @click="editModal?.open(domain)"
                                            >
                                                <IconPencil class="size-3.5" />
                                                <span class="sr-only">Edit</span>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>Edit</TooltipContent>
                                    </Tooltip>

                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="size-8 text-muted-foreground hover:text-destructive"
                                                :data-testid="`domain-delete-${domain.id}`"
                                                @click="confirmDelete(domain)"
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
                :icon="IconWorld"
                title="No domains yet"
                description="Domains are used to create branded short links. e.g. link.yourdomain.com/short-link"
                class="p-4 sm:p-6"
            />
        </div>
    </AppLayout>
</template>
