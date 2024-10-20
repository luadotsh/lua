<script setup>
import AuthLayout from "@/Layouts/Auth.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Social from "./Partial/Social.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: true,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Log in" />
        <div class="mb-6">
            <div class="flex justify-center">
                <img src="/images/mercantive/color.png" class="h-10" />
            </div>

            <h1
                class="mt-10 text-center text-2xl font-medium leading-9 text-zinc-800 dark:text-zinc-300"
            >
                Sign in to Lua.sh
            </h1>
        </div>

        <!-- <Social /> -->

        <form @submit.prevent="submit">
            <div>
                <Label for="email" value="Email" :required="true" />

                <Input
                    id="email"
                    type="email"
                    name="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <Label for="password" value="Password" :required="true" />

                <Input
                    id="password"
                    name="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Button
                    :class="{
                        'btn-primary w-full': true,
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    Sign in
                </Button>
            </div>
        </form>

        <div class="mt-6 flex items-center justify-center space-x-6">
            <Link
                :href="route('password.request')"
                class="text-sm hover:underline link"
            >
                Forgot your password?
            </Link>

            <Link
                :href="route('register')"
                class="text-sm hover:underline link"
            >
                Sign up
            </Link>
        </div>
    </AuthLayout>
</template>
