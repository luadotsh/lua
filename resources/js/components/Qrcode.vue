<script setup lang="ts">
import { ref, watch } from "vue";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { copyToClipboard } from "@/lib/utils";
import { qrCode as qrCodeRoute } from "@/routes/api";

interface LinkData {
    id: string | number;
    link?: string;
}

const colors = [
    "#000000",
    "#FF6B6B",
    "#FAB005",
    "#15AABF",
    "#FF922B",
    "#7048E8",
    "#2F9E44",
];

const linkIsCopied = ref(false);
const color = ref<string>("#000000");
const link = ref<LinkData | null>(null);
const imageUrl = ref("");
const isOpen = ref(false);

const open = (l: LinkData) => {
    link.value = l;
    color.value = colors[0];
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
        imageUrl.value = qrCodeRoute.url(link.value.id, {
            query: { color: color.value },
        });
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

            <div class="flex items-center justify-between space-x-4">
                <div class="flex items-center space-x-2 overflow-x-auto p-2">
                    <button
                        v-for="hex in colors"
                        :key="hex"
                        type="button"
                        class="relative flex items-center justify-center rounded-full p-0.5 focus:outline-none"
                        :class="color === hex ? 'ring-2 ring-offset-1' : ''"
                        :style="color === hex ? { '--tw-ring-color': hex } : {}"
                        @click="color = hex"
                    >
                        <span
                            class="h-[30px] w-[30px] rounded-full block"
                            :style="{ backgroundColor: hex }"
                        />
                    </button>
                </div>
                <div class="relative">
                    <Input
                        type="text"
                        v-model="color"
                        placeholder="#000000"
                        maxlength="7"
                        class="pr-10"
                    />
                    <div class="absolute inset-y-0 right-0 flex py-1.5 pr-1">
                        <color-picker
                            v-model:pureColor="color"
                            format="hex"
                            shape="square"
                            pickerType="chrome"
                            :disableAlpha="true"
                            :disableHistory="true"
                            :roundHistory="true"
                            :debounce="75"
                        />
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button
                    variant="outline"
                    @click="copyToClipboard(imageUrl, 'QR code link copied')"
                >
                    {{ linkIsCopied ? "Copied!" : "Copy link" }}
                </Button>
                <Button as-child>
                    <a
                        :href="link ? qrCodeRoute.url(link.id, { query: { color, download: '1' } }) : '#'"
                        target="_blank"
                    >
                        Download
                    </a>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
