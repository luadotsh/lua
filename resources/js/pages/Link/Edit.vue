<script setup lang="ts">
import { Head, router, useForm } from "@inertiajs/vue3";
import { IconQrcode, IconTrash } from "@tabler/icons-vue";
import { ref } from "vue";
import ConfirmDeleteModal from "@/components/ConfirmDeleteModal.vue";
import Qrcode from "@/components/Qrcode.vue";
import { Button } from "@/components/ui/button";
import AppLayout from "@/layouts/AppLayout.vue";
import dayjs from "@/dayjs";
import * as linksRoute from "@/routes/links";
import type { BreadcrumbItem } from "@/types";
import LinkForm from "./LinkForm.vue";

interface Tag {
    id: string | number;
    name: string;
}

interface LinkData {
    id: string | number;
    link: string;
    url: string;
    domain: string;
    key: string;
    tags: Tag[];
    // Nullable rather than optional: these come straight off the model, where
    // an unset column is null, not absent.
    ios?: string | null;
    android?: string | null;
    password?: string | null;
    expires_at?: string | null;
    expired_redirect_url?: string | null;
    utm_source?: string | null;
    utm_medium?: string | null;
    utm_campaign?: string | null;
    utm_term?: string | null;
    utm_content?: string | null;
}

const { link } = defineProps<{ link: LinkData }>();

// The model hands over null for every column left unset, but the inputs bind to
// strings — an unfilled field would otherwise render the word "null".
const text = (value: string | null | undefined): string => value ?? "";

const form = useForm({
    ...link,
    tags: link.tags.map((t) => t.id),
    ios: text(link.ios),
    android: text(link.android),
    password: text(link.password),
    expired_redirect_url: text(link.expired_redirect_url),
    utm_source: text(link.utm_source),
    utm_medium: text(link.utm_medium),
    utm_campaign: text(link.utm_campaign),
    utm_term: text(link.utm_term),
    utm_content: text(link.utm_content),
    expires_at: text(link.expires_at),
});

const expiresAtDate = ref(
    link.expires_at ? dayjs(link.expires_at).format("YYYY-MM-DDTHH:mm") : "",
);

// The link itself is the leaf: it says which one you are editing, which the
// word "Edit" on its own does not.
const breadcrumbs: BreadcrumbItem[] = [
    { title: "Links", href: linksRoute.index.url() },
    { title: link.link },
];

const qrcodeModal = ref<InstanceType<typeof Qrcode> | null>(null);
const confirmDeleteModal = ref<InstanceType<typeof ConfirmDeleteModal> | null>(null);

const update = () => {
    form.expires_at = expiresAtDate.value
        ? dayjs(expiresAtDate.value).utc().format("YYYY-MM-DD HH:mm:ss")
        : "";

    form.put(linksRoute.update.url(link.id));
};
</script>

<template>
    <Head :title="link.link" />

    <Qrcode ref="qrcodeModal" />
    <ConfirmDeleteModal
        ref="confirmDeleteModal"
        description="Are you sure you want to delete this link?"
        @deleted="router.visit(linksRoute.index.url())"
    />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #header-actions>
            <Button variant="ghost" size="icon" @click="qrcodeModal?.open(link)">
                <IconQrcode class="size-4" />
                <span class="sr-only">QR code</span>
            </Button>
            <Button
                variant="ghost"
                size="icon"
                class="text-destructive hover:text-destructive"
                @click="confirmDeleteModal?.open({ url: linksRoute.destroy.url(link.id) })"
            >
                <IconTrash class="size-4" />
                <span class="sr-only">Delete link</span>
            </Button>
            <Button :disabled="form.processing" @click="update">Save changes</Button>
        </template>

        <div class="mx-auto w-full max-w-3xl p-4 sm:p-6">
            <LinkForm :form="form" v-model:expires-at-date="expiresAtDate" />
        </div>
    </AppLayout>
</template>
