<script setup lang="ts">
import { computed, ref, watch } from "vue";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import HexColorInput from "@/components/HexColorInput.vue";
import { absoluteUrl, copyToClipboard } from "@/lib/utils";
import { qrCode as qrCodeRoute } from "@/routes/api";

interface LinkData {
    id: string | number;
    link?: string;
}

const DEFAULT_COLOR = "#000000";

const linkIsCopied = ref(false);
const color = ref<string>(DEFAULT_COLOR);

// The picker can hand back null when its field is emptied, but a QR code always
// needs a colour — an empty one would render the code invisible.
const pickedColor = computed({
    get: () => color.value,
    set: (value: string | null) => {
        color.value = value ?? DEFAULT_COLOR;
    },
});
const link = ref<LinkData | null>(null);
const imageUrl = ref("");
const isOpen = ref(false);

const open = (l: LinkData) => {
    link.value = l;
    color.value = DEFAULT_COLOR;
    isOpen.value = true;
};

defineExpose({ open });

watch(linkIsCopied, (value) => {
    if (value) {
        setTimeout(() => {
            linkIsCopied.value = false;
        }, 1000);
    }
});

watch(
    [color, link],
    () => {
        if (!link.value) return;
        imageUrl.value = absoluteUrl(
            qrCodeRoute.url(link.value.id, { query: { color: color.value } }),
        );
    },
    { immediate: true }
);
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>QR Code</DialogTitle>
            </DialogHeader>

            <div class="flex items-center justify-center p-8 bg-muted border border-border rounded-lg">
                <img
                    v-if="imageUrl"
                    :src="imageUrl"
                    alt="QR Code"
                    class="max-h-44 h-full rounded-lg border border-border"
                />
            </div>

            <HexColorInput v-model="pickedColor" placeholder="#000000" />

            <DialogFooter>
                <Button
                    variant="outline"
                    @click="copyToClipboard(imageUrl, 'QR code link copied')"
                >
                    {{ linkIsCopied ? "Copied!" : "Copy link" }}
                </Button>
                <Button as-child>
                    <a
                        :href="link ? absoluteUrl(qrCodeRoute.url(link.id, { query: { color, download: '1' } })) : '#'"
                        target="_blank"
                    >
                        Download
                    </a>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
