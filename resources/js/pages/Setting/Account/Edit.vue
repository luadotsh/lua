<script setup lang="ts">
import { usePage, useForm, Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import Avatar from "./Avatar.vue";
import * as accountRoutes from "@/routes/setting/account";
import { edit as authenticationEdit } from "@/routes/setting/authentication";

defineProps<{
    mustVerifyEmail?: boolean;
    status?: boolean;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const update = () => {
    form.post(accountRoutes.update.url(), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Account" />

    <AppLayout>
        <SettingsLayout>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Account Information</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Update your account's name and email.
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

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6"
                >
                    <Label for="email">Email <span class="text-red-500">*</span></Label>
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Input
                            id="email"
                            v-model="form.email"
                            type="text"
                            autocomplete="email"
                        />
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>
                </div>

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6"
                >
                    <Label for="avatar">Avatar</Label>
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Avatar />
                    </div>
                </div>

                <div
                    class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6"
                >
                    <div>
                        <Label>Password</Label>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Passwords, connected accounts and sessions live on
                            their own page.
                        </p>
                    </div>
                    <div class="mt-2 sm:col-span-2 sm:mt-0">
                        <Link
                            :href="authenticationEdit().url"
                            class="text-sm underline underline-offset-4 hover:text-foreground"
                        >
                            Go to authentication settings
                        </Link>
                    </div>
                </div>

            </div>
        </SettingsLayout>
    </AppLayout>
</template>
