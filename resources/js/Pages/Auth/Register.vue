<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { register as registerRoute } from '@/routes';
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

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    autofocus
                    required
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
                    required
                    placeholder="Your email"
                />
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    required
                    placeholder="Min. 8 characters"
                />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                Create Account
            </Button>
        </form>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <Link href="/login" class="underline underline-offset-4 hover:text-foreground">
                Sign in
            </Link>
        </div>
    </AuthLayout>
</template>
