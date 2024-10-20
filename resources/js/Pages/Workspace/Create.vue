<script setup>
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Dropdown from "@/Components/Dropdown.vue";
import InputHelp from "@/Components/InputHelp.vue";
import Banner from "@/Components/Banner.vue";

import { Head, Link, useForm, usePage } from "@inertiajs/vue3";

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
    <div class="min-h-screen flex">
        <div
            class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 dark:bg-zinc-800"
        >
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <h1 class="page-title mb-4">New Workspace</h1>
                    <div class="page-description">
                        Workspaces are where you manage your links, domains,
                        tags and more.
                    </div>
                </div>

                <div class="mt-8">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="name" value="Name" :required="true" />

                            <Input
                                id="name"
                                type="text"
                                v-model="form.name"
                                required
                                autofocus
                                class="mt-1"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.name"
                            />
                        </div>

                        <div class="space-y-4 mt-4">
                            <div>
                                <Button
                                    :class="{
                                        'btn-primary w-full': true,
                                        'opacity-25': form.processing,
                                    }"
                                    :disabled="form.processing"
                                >
                                    Create Workspace
                                </Button>
                            </div>
                            <div v-if="user.workspaces.length >= 1">
                                <a
                                    :href="route('links.index')"
                                    class="cursor-pointer text-zinc-800 dark:text-zinc-300 hover:text-zinc-500 font-semibold text-sm w-full flex items-center justify-center"
                                >
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div
            class="relative hidden w-0 flex-1 lg:flex items-center justify-center flex-col bg-zinc-900"
        >
            <img src="/images/store/bg.png" alt="Sign up" />

            <div class="absolute flex items-center space-x-4 right-6 bottom-6">
                <div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="cursor-pointer text-white font-normal text-xs hover:underline w-full flex items-center justify-center"
                    >
                        Sign out
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
