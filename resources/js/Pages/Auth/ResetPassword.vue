<script setup>
import AuthLayout from "@/Layouts/Auth.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Reset Password" />

        <div class="mb-6">
            <h1 class="page-title text-center">Create new password</h1>
        </div>

        <form @submit.prevent="submit">
            <div>
                <Label for="email" value="Email" :required="true" />

                <Input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    readonly
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <Label for="password" value="Password" :required="true" />

                <Input
                    id="password"
                    type="password"
                    v-model="form.password"
                    placeholder="Password with 8+ characters"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <Label
                    for="password_confirmation"
                    value="Confirm Password"
                    :required="true"
                />

                <Input
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    placeholder="Password with 8+ characters"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Button
                    :class="{
                        'btn-primary w-full': true,
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    Reset Password
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
