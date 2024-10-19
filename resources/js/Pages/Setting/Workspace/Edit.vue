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

defineProps({
    currencies: Object,
});

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

        <div class="mx-auto w-full max-w-7xl px-4 py-12">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="setting-page-title">Workspace Settings</h2>
                    <p class="setting-page-subtitle">
                        Update your workspace's.
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
                    <Label for="logo" value="Logo" :required="true" />
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

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-center sm:gap-4 sm:py-6"
                >
                    <div>
                        <Label
                            for="subdomain"
                            value="Store URL"
                            :required="true"
                        />
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Your store is live at this link
                        </p>
                    </div>
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <div class="relative flex items-center">
                            <Input
                                id="subdomain"
                                type="text"
                                v-model="form.subdomain"
                                class="pr-36"
                                pattern="^[a-z0-9-]+$"
                                placeholder="tesla"
                                required
                            />
                            <div
                                class="absolute inset-y-0 right-0 flex py-1.5 pr-1.5"
                            >
                                <span
                                    class="inline-flex items-center select-none pr-1 text-zinc-400 dark:text-zinc-300 text-sm"
                                >
                                    .mercantive.com
                                </span>
                            </div>
                        </div>

                        <InputError
                            class="mt-2"
                            :message="form.errors.subdomain"
                        />
                    </div>
                </div>

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-center sm:gap-4 sm:py-6"
                >
                    <div>
                        <Label
                            for="support_email"
                            value="Support email"
                            :required="true"
                        />
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            This is where customers can reach you for
                            assistance, displayed on their receipts and
                            invoices.
                        </p>
                    </div>

                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Input
                            id="support_email"
                            type="text"
                            v-model="form.support_email"
                            placeholder=""
                            required
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.support_email"
                        />
                    </div>
                </div>

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-center sm:gap-4 sm:py-6"
                >
                    <Label
                        for="currency_id"
                        value="Currency"
                        :required="true"
                    />
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Dropdown
                            id="currency_id"
                            :options="
                                currencies.map((currency) => ({
                                    id: currency.id,
                                    label: currency.label,
                                }))
                            "
                            class="w-full"
                            :search="true"
                            placeholder="Select..."
                            v-model="form.currency_id"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.currency"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
