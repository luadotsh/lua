<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login as loginRoute } from '@/routes';
import { request as forgotPasswordRoute } from '@/routes/password';
import Social from '@/pages/Auth/Partial/Social.vue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(loginRoute().url, {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthLayout title="Sign in" description="Enter your email and password to sign in">
        <Head title="Log in" />

        <Social />

        <div v-if="status" class="text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="username"
                    autofocus
                    required
                />
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <div class="grid gap-2">
                <div class="flex items-center">
                    <Label for="password">Password</Label>
                    <Link
                        :href="forgotPasswordRoute()"
                        class="ml-auto text-sm text-muted-foreground underline underline-offset-4 hover:text-foreground"
                    >
                        Forgot your password?
                    </Link>
                </div>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    required
                />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                Sign in
            </Button>
        </form>

        <div class="text-center text-sm text-muted-foreground">
            Don't have an account?
            <Link href="/register" class="underline underline-offset-4 hover:text-foreground">
                Sign up
            </Link>
        </div>
    </AuthLayout>
</template>
