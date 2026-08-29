<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { index as linksIndex } from '@/routes/links';
import { store as workspacesStore } from '@/routes/workspaces';

const user = usePage().props.auth.user;

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(workspacesStore().url, {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="New Workspace" />

    <AuthLayout
        title="New workspace"
        description="Workspace is a place where you can organize your links"
    >
        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    autofocus
                    required
                    placeholder="Enter the workspace name"
                />
                <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                {{ form.processing ? 'Creating...' : 'Continue' }}
            </Button>

            <Link
                v-if="user.workspaces.length >= 1"
                :href="linksIndex()"
                class="text-center text-sm text-muted-foreground hover:text-foreground"
            >
                Cancel
            </Link>
        </form>
    </AuthLayout>
</template>
