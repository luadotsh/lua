<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { accept } from '@/routes/auth/invites';

const props = defineProps<{
    id: string;
    name?: string;
    email?: string;
    status?: string;
}>();

const form = useForm({
    id: props.id,
    email: props.email ?? '',
    name: props.name ?? '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(accept(form.id).url, {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <AuthLayout title="Accept Invitation" description="Create your account to join the workspace">
        <Head title="Invite" />

        <div v-if="status" class="text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    autofocus
                    required
                    autocomplete="off"
                />
                <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    readonly
                    autocomplete="off"
                />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    placeholder="Create your password"
                    autocomplete="off"
                />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm Password</Label>
                <Input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    placeholder="Repeat your password"
                    autocomplete="off"
                />
                <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">{{ form.errors.password_confirmation }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                Create Account
            </Button>
        </form>
    </AuthLayout>
</template>
