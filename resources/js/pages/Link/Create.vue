<script setup lang="ts">
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { Button } from "@/components/ui/button";
import AppLayout from "@/layouts/AppLayout.vue";
import dayjs from "@/dayjs";
import * as linksRoute from "@/routes/links";
import type { BreadcrumbItem } from "@/types";
import LinkForm from "./LinkForm.vue";

const domains = usePage().props.domains as string[];

const form = useForm({
    url: "",
    domain: domains[0] ?? "",
    key: "",
    tags: [] as (string | number)[],
    ios: "",
    android: "",
    expires_at: "",
    expired_redirect_url: "",
    password: "",
    utm_source: "",
    utm_medium: "",
    utm_campaign: "",
    utm_term: "",
    utm_content: "",
});

const expiresAtDate = ref("");

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Links", href: linksRoute.index.url() },
    { title: "New link" },
];

const store = () => {
    form.expires_at = expiresAtDate.value
        ? dayjs(expiresAtDate.value).utc().format("YYYY-MM-DD HH:mm:ss")
        : "";

    form.post(linksRoute.store.url());
};
</script>

<template>
    <Head title="New link" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #header-actions>
            <Button variant="ghost" as-child>
                <Link :href="linksRoute.index.url()">Cancel</Link>
            </Button>
            <Button :disabled="form.processing" @click="store">Create link</Button>
        </template>

        <div class="mx-auto w-full max-w-3xl p-4 sm:p-6">
            <LinkForm :form="form" v-model:expires-at-date="expiresAtDate" />
        </div>
    </AppLayout>
</template>
