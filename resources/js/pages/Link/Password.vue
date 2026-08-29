<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import InputError from "@/components/InputError.vue";

import { validate as validatePasswordRoute } from "@/routes/links/password";

interface LinkData {
    key: string;
}

const { link } = defineProps<{
    link: LinkData;
}>();

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(validatePasswordRoute.url(link.key));
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
                    <Label for="password">
                        Password <span class="text-red-500">*</span>
                    </Label>

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
                    <Button type="submit" class="w-full">
                        Unlock
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
