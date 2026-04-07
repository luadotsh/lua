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
    ios?: string;
    android?: string;
    password?: string;
    expires_at?: string;
    expired_redirect_url?: string;
}

const { link } = defineProps<{ link: LinkData }>();

const form = useForm({
    ...link,
    tags: link.tags.map((t) => t.id),
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
