<script setup>
import { ref } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/Master.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Logo from "./Logo.vue";

const workspace = usePage().props.auth.user.current_workspace;

const form = useForm(workspace);

const update = () => {
    form.put(route("setting.workspace.update"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Workspace Settings" />

    <AppLayout>
        <template #header>
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="page-title">Settings</h1>
                </div>
            </div>
        </template>

        <div class="mx-auto w-full max-w-7xl lg:px-4 lg:py-12">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="setting-page-title">Workspace Settings</h2>
                    <p class="setting-page-subtitle">
                        Here you can update your workspace settings like name,
                        logo, etc.
                    </p>
                </div>
                <div>
                    <Button
                        :class="{
                            'flex items-center space-x-1.5 btn-primary': true,
                            'opacity-25': form.processing,
                        }"
                        :disabled="form.processing"
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
                    <Label for="logo" value="Logo" />
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Logo />
                    </div>
                </div>
                <div
                    class="sm:grid sm:grid-cols-3 sm:items-center sm:gap-4 sm:py-6"
                >
                    <Label for="name" value="Name" :required="true" />
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="name"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
