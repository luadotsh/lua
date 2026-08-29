<script setup lang="ts">
import { Head, Link, usePage, useForm } from "@inertiajs/vue3";
import HeadingSmall from "@/components/HeadingSmall.vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import * as accountRoutes from "@/routes/setting/account";
import { send } from "@/routes/verification";
import Avatar from "./Avatar.vue";

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const update = () => {
    form.post(accountRoutes.update.url());
};
</script>

<template>
    <Head title="Account" />

    <AppLayout>
        <SettingsLayout
            title="Account"
            description="Your name, email and photo."
        >
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Photo"
                    description="This is how you show up across the app."
                />

                <Avatar />
            </div>

            <Separator />

            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Account information"
                    description="Update your account's name and email."
                />

                <form class="space-y-6" @submit.prevent="update">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="name"
                            placeholder="Your full name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="username"
                            placeholder="you@example.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-foreground/70">
                            Your email address is unverified.
                            <Link
                                :href="send()"
                                as="button"
                                class="font-semibold text-foreground underline decoration-foreground/30 underline-offset-4 transition-colors hover:decoration-foreground"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-semibold text-emerald-700"
                        >
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <Button :disabled="form.processing">Save changes</Button>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
