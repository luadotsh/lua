<script setup lang="ts">
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
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
import { IconDots, IconWorld } from "@tabler/icons-vue";
import DomainStatus from "@/components/DomainStatus.vue";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";
import * as domainsRoutes from "@/routes/setting/domains";

const createModal = ref<InstanceType<typeof CreateModal> | null>(null);
const editModal = ref<InstanceType<typeof EditModal> | null>(null);
const domainToDelete = ref<{ id: string | number } | null>(null);

const props = defineProps<{
    domains: object;
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

    <AppLayout>
        <SettingsLayout>
            <CreateModal ref="createModal" />
            <EditModal ref="editModal" />

            <AlertDialog :open="!!domainToDelete" @update:open="(val) => !val && (domainToDelete = null)">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Delete Domain</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to delete this domain?
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="domainToDelete = null">Cancel</AlertDialogCancel>
                        <AlertDialogAction @click="deleteDomain" class="bg-red-600 hover:bg-red-700">Delete</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold">Domains</h2>
                <Button @click="createModal?.open()">
                    New Domain
                </Button>
            </div>

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
                                    :src="route('websites.favicon', { url: domain.domain })"
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

                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" size="icon">
                                        <IconDots class="h-4 w-4 text-zinc-400" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem @click="editModal?.open(domain)">
                                        Edit
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem
                                        class="text-red-600 focus:text-red-600"
                                        @click="confirmDelete(domain)"
                                    >
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="!hasData"
                class="flex flex-col items-center justify-center py-12 text-center"
            >
                <IconWorld class="h-12 w-12 text-zinc-300 mb-4" />
                <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">You don't have any domains yet.</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Domains are used to create branded short links. e.g. link.yourdomain.com/short-link</p>
                <Button class="mt-4" @click="createModal?.open()">Add Domain</Button>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
