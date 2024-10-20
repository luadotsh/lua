<script setup>
import { ref, watch, onMounted } from "vue";
import QRCode from "qrcode";

import Button from "./Button.vue";
import DialogModal from "@/Components/DialogModal.vue";

// Reference to hold the QR code image
const qrCodeImage = ref("");
const url = ref("");

// Function to generate the QR code
const generateQRCode = async () => {
    if (url.value) {
        try {
            const qrCodeDataUrl = await QRCode.toDataURL(url.value);
            qrCodeImage.value = qrCodeDataUrl;
        } catch (error) {
            console.error("QR Code generation failed:", error);
        }
    }
};

const isOpen = ref(false);

const open = (u) => {
    url.value = u;
    isOpen.value = true;
};

defineExpose({ open });

// Watch the  URL prop and regenerate the QR code if the URL changes
watch(() => url.value, generateQRCode);

// Generate the QR code when the component mounts
onMounted(generateQRCode);
</script>

<template>
    <DialogModal max-width="md" :show="isOpen" @close="isOpen = false">
        <template #title>QR Code</template>

        <template #content>
            <div class="flex items-center justify-center p-6">
                <img
                    v-if="qrCodeImage"
                    :src="qrCodeImage"
                    alt="QR Code"
                    class="h-48 rounded-lg"
                />
            </div>
        </template>
    </DialogModal>
</template>

<style scoped>
.qrcode-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
}

.qr-code {
    width: 200px;
    height: 200px;
}
</style>
