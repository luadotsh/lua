<script setup>
import AuthLayout from "@/Layouts/Auth.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Social from "./Partial/Social.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    password: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Register" />
    <AuthLayout>
        <div class="mb-6">
            <div class="flex justify-center">
                <img src="/images/mercantive/color.png" class="h-10" />
            </div>

            <h1
                class="mt-10 text-center text-2xl font-medium leading-9 text-zinc-800 dark:text-zinc-300"
            >
                Sign up to Lua.sh
            </h1>
        </div>

        <!-- <Social /> -->

        <div>
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <Label for="name" value="Name" :required="true" />

                    <Input
                        id="name"
                        type="text"
                        name="name"
                        v-model="form.name"
                        required
                        autofocus
                        placeholder="Your name"
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <Label for="email" value="Email" :required="true" />

                    <Input
                        id="email"
                        type="email"
                        name="email"
                        v-model="form.email"
                        required
                        placeholder="Your email"
                        autocomplete="email"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <Label for="password" value="Password" :required="true" />

                    <Input
                        id="password"
                        type="password"
                        name="password"
                        v-model="form.password"
                        required
                        placeholder="Min. 8 characters"
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-xs text-zinc-500">
                        Signing up for a Lua.sh you agree to the Privacy Policy
                        and Terms of Service.
                    </p>
                </div>

                <div>
                    <Button
                        :class="{
                            'btn-primary w-full': true,
                            'opacity-25': form.processing,
                        }"
                        :disabled="form.processing"
                    >
                        Create Account
                    </Button>
                </div>
            </form>
        </div>
        <div>
            <div class="flex items-center justify-center mt-4">
                <Link
                    :href="route('login')"
                    class="text-sm hover:underline link"
                >
                    Already registered? Sign in
                </Link>
            </div>
        </div>
    </AuthLayout>
</template>
