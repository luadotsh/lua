<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Button from "@/Components/Button.vue";

const { link } = defineProps({
    link: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("links.password.validate", link.key));
};
</script>

<template>
    <div
        class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img src="/images/lua/full-black.svg" class="mx-auto h-16 w-auto" />
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <Label for="password" value="Password" :required="true" />

                    <Input
                        id="password"
                        type="password"
                        name="password"
                        v-model="form.password"
                        required
                        autofocus
                        placeholder="Enter the password"
                        autocomplete="password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <Button type="submit" class="btn btn-primary w-full">
                        Unlock
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
