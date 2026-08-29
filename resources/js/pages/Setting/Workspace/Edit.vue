<script setup lang="ts">
import { Head, useForm, usePage } from "@inertiajs/vue3";
import HeadingSmall from "@/components/HeadingSmall.vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import * as workspaceRoutes from "@/routes/setting/workspace";
import Logo from "./Logo.vue";

const workspace = usePage().props.auth.user.current_workspace;

const form = useForm(workspace);

const update = () => {
    form.put(workspaceRoutes.update.url(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Workspace" />

    <AppLayout>
        <SettingsLayout
            title="Workspace"
            description="Your workspace's name and logo."
        >
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Logo"
                    description="This is how your workspace shows up across the app."
                />

                <Logo />
            </div>

            <Separator />

            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Workspace information"
                    description="Update your workspace's name."
                />

                <form class="space-y-6" @submit.prevent="update">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="organization"
                            placeholder="Your workspace name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <Button :disabled="form.processing">Save changes</Button>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
