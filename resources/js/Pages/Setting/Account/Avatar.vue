<script setup lang="ts">
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { IconTrash } from "@tabler/icons-vue";
import axios from "axios";
import * as mediasRoutes from "@/routes/medias";

const input = ref<HTMLInputElement | null>(null);
const uploadHasErrors = ref<string | null>(null);
const isLoading = ref(false);

const user = computed(() => usePage().props.auth.user);

const upload = async () => {
    input.value!.click();

    input.value!.onchange = async (event: Event) => {
        isLoading.value = true;

        const formData = new FormData();
        formData.append("media", (event.target as HTMLInputElement).files![0]);
        formData.append("model", "User");
        formData.append("model_id", user.value.id);
        formData.append("collection", "photos");
        formData.append("visibility", "public");

        await axios
            .post(mediasRoutes.store.url(), formData)
            .then(() => {
                uploadHasErrors.value = null;
                router.reload();
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                isLoading.value = false;
                input.value!.value = "";
            });
    };
};

const destroy = () => {
    router.delete(
        mediasRoutes.destroy.url({
            modelId: user.value.media?.[0].model_id,
            id: user.value.media?.[0].id,
        }),
        {
            onSuccess: () => {
                router.reload();
            },
        }
    );
};
</script>

<template>
    <input type="file" class="hidden" ref="input" />

    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div>
                <img
                    :src="user.photo_url"
                    :alt="user.name"
                    class="h-10 w-10 rounded-full"
                />
            </div>
            <div>
                <Button
                    variant="outline"
                    size="sm"
                    @click="upload"
                    :disabled="isLoading"
                    :class="{ 'opacity-25': isLoading }"
                >
                    Choose
                </Button>
            </div>
            <div class="text-sm text-zinc-500">
                JPG, PNG, or JPEG. Max size of 1MB
            </div>
        </div>

        <div
            v-if="user.media?.length >= 1"
            @click="destroy"
            class="p-2 hover:bg-zinc-100 rounded-md cursor-pointer"
        >
            <IconTrash class="text-zinc-500 h-5 w-5" />
        </div>
    </div>
    <div v-show="uploadHasErrors" class="my-2">
        <p class="text-sm text-red-600">
            {{ uploadHasErrors }}
        </p>
    </div>
</template>
