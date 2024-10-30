<script setup>
import { usePage, useForm, Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/Master.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Avatar from "./Avatar.vue";
import Dropdown from "@/Components/Dropdown.vue";

const user = usePage().props.auth.user;

defineProps({
    mustVerifyEmail: Boolean,
    status: Boolean,
});

const form = useForm({
    name: user.name,
    email: user.email,
    theme: user.theme,
    current_password: "",
    password: "",
    password_confirmation: "",
});

const update = () => {
    form.post(route("setting.account.update"), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Account" />

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
                    <h2 class="setting-page-title">Account Information</h2>
                    <p class="setting-page-subtitle">
                        Update your account's name, email and password.
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
                    class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6"
                >
                    <Label for="email" value="Email" :required="true" />
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Input
                            id="email"
                            v-model="form.email"
                            type="text"
                            autocomplete="email"
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                </div>

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6"
                >
                    <Label for="avatar" value="Avatar" />
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Avatar />
                    </div>
                </div>

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6"
                >
                    <div>
                        <Label for="current_password" value="Password" />
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Confirm your current password before setting a new
                            one. 8 characters minimum.
                        </p>
                    </div>
                    <div class="mt-2 sm:col-span-2 sm:mt-0 space-y-2">
                        <div>
                            <Input
                                id="current_password"
                                v-model="form.current_password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Current Password"
                            />

                            <InputError
                                :message="form.errors.password"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="New Password"
                            />

                            <InputError
                                :message="form.errors.password"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                autocomplete="current-password"
                                placeholder="Confirm Password"
                            />
                            <InputError
                                :message="form.errors.password_confirmation"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6"
                >
                    <Label for="theme" value="Theme" :required="true" />

                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Dropdown
                            id="theme"
                            :search="true"
                            :options="[
                                {
                                    id: 'LIGHT',
                                    label: 'Light',
                                },
                                {
                                    id: 'DARK',
                                    label: 'Dark',
                                },
                                {
                                    id: 'SYSTEM',
                                    label: 'System',
                                },
                            ]"
                            class="w-full"
                            v-model="form.theme"
                        />

                        <InputError :message="form.errors.theme" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
./Avatar.vue
