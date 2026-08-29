<script setup lang="ts">
import { Head, useForm, usePage } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import Logo from "./Logo.vue";
import * as workspaceRoutes from "@/routes/setting/workspace";

const workspace = usePage().props.auth.user.current_workspace;

const form = useForm(workspace);

const update = () => {
    form.put(workspaceRoutes.update.url(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Workspace Settings" />

    <AppLayout>
        <SettingsLayout>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Workspace Settings</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Here you can update your workspace settings like name,
                        logo, etc.
                    </p>
                </div>
                <div>
                    <Button
                        :disabled="form.processing"
                        :class="{ 'opacity-25': form.processing }"
                        @click="update"
                    >
                        Save Changes
                    </Button>
                </div>
            </div>
            <div
                class="mt-6 space-y-8 border-b border-zinc-200 dark:border-zinc-700 pb-12 sm:space-y-0 sm:divide-y sm:divide-zinc-200 sm:dark:divide-zinc-700 sm:border-t sm:pb-0"
            >
                <div
                    class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6"
                >
                    <Label for="logo">Logo</Label>
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Logo />
                    </div>
                </div>
                <div
                    class="sm:grid sm:grid-cols-3 sm:items-center sm:gap-4 sm:py-6"
                >
                    <Label for="name">Name <span class="text-red-500">*</span></Label>
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="name"
                        />
                        <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
