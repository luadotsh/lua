<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send as verificationSendRoute } from '@/routes/verification';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(verificationSendRoute().url);
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <AuthLayout title="Verify your email" description="Please verify your email address to continue">
        <Head title="Email Verification" />

        <p class="text-sm text-muted-foreground">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </p>

        <div v-if="verificationLinkSent" class="text-sm font-medium text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <div class="flex items-center justify-between gap-4">
                <Button type="submit" :disabled="form.processing">
                    Resend Verification Email
                </Button>

                <Link
                    :href="logout()"
                    method="post"
                    as="button"
                    class="text-sm text-muted-foreground underline underline-offset-4 hover:text-foreground"
                >
                    Log out
                </Link>
            </div>
        </form>
    </AuthLayout>
</template>
