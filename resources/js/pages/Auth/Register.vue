<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import PasswordInput from '@/components/PasswordInput.vue';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login as loginRoute, register as registerRoute } from '@/routes';
import Social from '@/pages/Auth/Partial/Social.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
});

const submit = () => {
    form.post(registerRoute().url, {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthLayout title="Create an account" description="Enter your details to get started" :show-terms="true">
        <Head title="Register" />

        <Social />

        <form class="flex flex-col gap-6" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    autofocus
                    placeholder="Your name"
                />
                <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    placeholder="Your email"
                />
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput
                    id="password"
                    v-model="form.password"
                    autocomplete="new-password"
                    placeholder="Min. 8 characters"
                />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <Button
                type="submit"
                class="w-full"
                data-testid="register-submit"
                :disabled="form.processing"
            >
                Create Account
            </Button>
        </form>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <Link
                :href="loginRoute()"
                data-testid="register-login-link"
                class="underline underline-offset-4 hover:text-foreground"
            >
                Sign in
            </Link>
        </div>
    </AuthLayout>
</template>
