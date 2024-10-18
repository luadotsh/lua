<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import AuthLayout from "@/Layouts/Auth.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";

const { id, name, email } = defineProps({
    name: String,
    email: String,
    id: String,
    status: String,
});

const form = useForm({
    id: id,
    email: email,
    name: name,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("auth.invites.accept", form.id), {
        onFinish: () => form.reset(),
    });
};
</script>
<template>
    <Head title="Invite" />
    <AuthLayout>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="">
                <Label for="name" value="Name" :required="true" />

                <Input
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="off"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <Label for="email" value="Email" :required="true" />
                <Input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    readonly
                    autocomplete="off"
                />
            </div>

            <div class="mt-4">
                <Label for="password" value="Password" :required="true" />
                <Input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    placeholder="Create your password"
                    autocomplete="off"
                />
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
                    required
                    placeholder="Repeat your password"
                    autocomplete="off"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Button
                    :class="{
                        'btn-primary': true,
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                >
                    Create Account
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
