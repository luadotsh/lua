<script setup lang="ts">
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";
import dayjs from "@/dayjs";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import LinkForm from "./LinkForm.vue";
import * as linksRoute from "@/routes/links";

interface Tag {
    id: string | number;
    name: string;
}

interface LinkData {
    id: string | number;
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
    link.expires_at ? dayjs(link.expires_at).format("YYYY-MM-DDTHH:mm") : ""
);

const store = () => {
    if (expiresAtDate.value) {
        form.expires_at = dayjs(expiresAtDate.value).utc().format("YYYY-MM-DD HH:mm:ss");
    } else {
        form.expires_at = "";
    }
    form.put(linksRoute.update.url(link.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
        },
    });
};
</script>

<template>
    <Dialog :open="true" @update:open="(val) => !val && router.visit(linksRoute.index.url())">
        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Edit Link</DialogTitle>
            </DialogHeader>

            <LinkForm :form="form" v-model:expires-at-date="expiresAtDate" />

            <DialogFooter>
                <Button @click="store" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                    Update Link
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
