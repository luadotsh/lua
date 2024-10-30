<script setup>
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Dropdown from "@/Components/Dropdown.vue";
import InputHelp from "@/Components/InputHelp.vue";
import Banner from "@/Components/Banner.vue";
import FormSection from "@/Components/FormSection.vue";

import { Head, Link, useForm, usePage } from "@inertiajs/vue3";

import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();

const user = usePage().props.auth.user;

const form = useForm({
    name: "",
});

const submit = () => {
    form.post(route("workspaces.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="New Workspace" />
    <Banner />

    <div
        class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8 bg-white dark:bg-zinc-800"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <div class="flex justify-center mb-8">
                <img
                    src="/images/lua/full-color.svg"
                    class="h-16 hidden dark:block"
                />
                <img
                    src="/images/lua/full-black.svg"
                    class="h-16 block dark:hidden"
                />
            </div>
            <div class="text-center">
                <h1 class="page-title">New workspace</h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Workspace is a place where you can organize your links
                </p>
            </div>

            <FormSection @submitted="submit">
                <template #form>
                    <div class="col-span-6">
                        <Label for="name" value="Name" :required="true" />

                        <Input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            placeholder="Enter the workspace name"
                            class="mt-1"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                </template>

                <template #actions>
                    <div class="flex flex-1 flex-col justify-center">
                        <Button
                            type="submit"
                            :class="{
                                'w-full btn-primary': true,
                                'opacity-25': form.processing,
                            }"
                            :disabled="form.processing"
                        >
                            <span>
                                {{
                                    form.processing ? "Creating..." : "Continue"
                                }}
                            </span>
                        </Button>
                        <div v-if="user.workspaces.length >= 1">
                            <a
                                :href="route('links.index')"
                                class="mt-4 cursor-pointer text-zinc-800 dark:text-zinc-300 hover:text-zinc-500 font-medium text-sm w-full flex items-center justify-center"
                            >
                                Cancel
                            </a>
                        </div>
                    </div>
                </template>
            </FormSection>
        </div>
    </div>
</template>
