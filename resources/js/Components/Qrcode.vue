<script setup>
import { ref, watch, onMounted } from "vue";
import { RadioGroup, RadioGroupLabel, RadioGroupOption } from "@headlessui/vue";
import axios from "axios";
import helper from "@/helper";

import Input from "@/Components/Input.vue";
import Button from "./Button.vue";
import DialogModal from "@/Components/DialogModal.vue";

const linkIsCopied = ref(false);
const color = ref(null);
const link = ref(null);
const loading = ref(false);
const imageUrl = ref(null);
const isOpen = ref(false);

const colors = [
    {
        hex: "#000000", // Black
    },
    {
        hex: "#FF6B6B", // Soft Red
    },
    {
        hex: "#FAB005", // Warm Yellow
    },
    {
        hex: "#15AABF", // Teal
    },
    {
        hex: "#FF922B", // Vibrant Orange
    },
    {
        hex: "#7048E8", // Vivid Purple
    },
    {
        hex: "#2F9E44", // Forest Green
    },
];

const handleCopyLink = () => {
    helper.copyToClipboard(imageUrl.value);
    linkIsCopied.value = true;
};

const open = (l) => {
    link.value = l;
    color.value = colors[0].hex;
    isOpen.value = true;
};

const close = () => {
    isOpen.value = false;
    link.value = null;
    console.log("closed");
};

defineExpose({ open });

watch(
    () => linkIsCopied.value,
    (value) => {
        setTimeout(() => {
            linkIsCopied.value = false;
        }, 1000);
    }
);

watch(
    () => color.value,
    (value) => {
        imageUrl.value = route("api.qr-code", {
            id: link.value.id,
            color: value,
        });
    }
);
</script>

<template>
    <DialogModal max-width="lg" :show="isOpen" @close="close">
        <template #title>QR Code</template>

        <template #content>
            <div
                class="flex items-center justify-center p-8 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg"
            >
                <img
                    v-if="imageUrl"
                    :src="imageUrl"
                    alt="QR Code"
                    class="max-h-44 h-full rounded-lg border border-zinc-200"
                />
            </div>
            <div>
                <div class="flex items-center justify-between space-x-4">
                    <RadioGroup v-model="color">
                        <div
                            class="flex items-center space-x-2 overflow-y-hidden overflow-x-auto p-2"
                        >
                            <RadioGroupOption
                                as="template"
                                v-for="color in colors"
                                :key="color.hex"
                                :value="color.hex"
                                v-slot="{ active, checked }"
                            >
                                <div
                                    :style="{ '--tw-ring-color': color.hex }"
                                    :class="[
                                        active && checked
                                            ? 'ring ring-offset-1'
                                            : '',
                                        !active && checked ? 'ring-2' : '',
                                        'relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 focus:outline-none',
                                    ]"
                                >
                                    <RadioGroupLabel
                                        as="span"
                                        class="sr-only"
                                        >{{ color.hex }}</RadioGroupLabel
                                    >
                                    <span
                                        aria-hidden="true"
                                        :class="[
                                            'h-[30px] w-[30px] rounded-full ',
                                        ]"
                                        :style="{
                                            backgroundColor: color.hex,
                                        }"
                                    />
                                </div>
                            </RadioGroupOption>
                        </div>
                    </RadioGroup>
                    <div class="my-4 relative">
                        <Input
                            id="color"
                            type="text"
                            name="color"
                            v-model="color"
                            placeholder="#000000"
                            maxlength="7"
                        />
                        <div class="absolute inset-y-0 right-0 flex py-1.5">
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
            </div>
        </template>
        <template #footer>
            <Button class="btn-secondary" @click="handleCopyLink">
                {{ linkIsCopied ? "The link is copied" : "Copy link" }}
            </Button>
            <a
                class="btn btn-primary"
                :href="
                    route('api.qr-code', {
                        id: link.id,
                        color: color.hex,
                        download: 1,
                    })
                "
            >
                Download
            </a>
        </template>
    </DialogModal>
</template>
