<script setup lang="ts">
import { useForm, usePage } from "@inertiajs/vue3";
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
});

const show = ref(false);
const expiresAtDate = ref("");

const open = () => {
    form.reset();
    form.clearErrors();
    expiresAtDate.value = "";
    show.value = true;
};

const close = () => {
    form.reset();
    form.clearErrors();
    expiresAtDate.value = "";
    show.value = false;
};

defineExpose({ open });

const store = () => {
    if (expiresAtDate.value) {
        form.expires_at = dayjs(expiresAtDate.value).utc().format("YYYY-MM-DD HH:mm:ss");
    }
    form.post(linksRoute.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            expiresAtDate.value = "";
            show.value = false;
        },
    });
};
</script>

<template>
    <Dialog :open="show" @update:open="(val) => !val && close()">
        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>New Link</DialogTitle>
            </DialogHeader>

            <LinkForm :form="form" v-model:expires-at-date="expiresAtDate" />

            <DialogFooter>
                <Button @click="store" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                    Generate Link
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
