<script setup>
import { computed } from "vue";
import AuthLayout from "@/Layouts/Auth.vue";
import Button from "@/Components/Button.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
    <AuthLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-zinc-800 dark:text-zinc-300">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div
            class="mb-4 font-medium text-sm text-green-600"
            v-if="verificationLinkSent"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <Button
                    :class="{
                        'btn-primary': true,
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </Button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="link text-sm"
                    >Log Out</Link
                >
            </div>
        </form>
    </AuthLayout>
</template>
