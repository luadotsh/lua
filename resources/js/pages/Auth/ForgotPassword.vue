<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { email as passwordEmailRoute } from '@/routes/password';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(passwordEmailRoute().url);
};
</script>

<template>
    <AuthLayout title="Reset your password" description="Enter your email to receive a reset link">
        <Head title="Forgot Password" />

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

            <Button type="submit" class="w-full" :disabled="form.processing">
                Send Password Reset Link
            </Button>
        </form>
    </AuthLayout>
</template>
