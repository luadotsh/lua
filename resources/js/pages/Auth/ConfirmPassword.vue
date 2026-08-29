<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { confirm as passwordConfirmRoute } from '@/routes/password';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(passwordConfirmRoute().url, {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <AuthLayout title="Confirm Password" description="This is a secure area — please confirm your password">
        <Head title="Confirm Password" />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    autofocus
                    required
                />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                Confirm
            </Button>
        </form>
    </AuthLayout>
</template>
