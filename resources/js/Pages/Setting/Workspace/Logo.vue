<script setup lang="ts">
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { IconTrash } from "@tabler/icons-vue";
import axios from "axios";
import * as mediasRoutes from "@/routes/medias";

const inputRef = ref<HTMLInputElement | null>(null);
const uploadHasErrors = ref<string | null>(null);
const isLoading = ref(false);

const workspace = computed(() => usePage().props.auth.user.current_workspace);

const upload = async () => {
    inputRef.value!.click();

    inputRef.value!.onchange = async (event: Event) => {
        isLoading.value = true;

        const formData = new FormData();
        formData.append("media", (event.target as HTMLInputElement).files![0]);
        formData.append("model", "Workspace");
        formData.append("model_id", workspace.value.id);
        formData.append("collection", "logos");
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
                inputRef.value!.value = "";
            });
    };
};

const destroy = () => {
    router.delete(
        mediasRoutes.destroy.url({
            modelId: workspace.value.media?.[0].model_id,
            id: workspace.value.media?.[0].id,
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
    <input type="file" ref="inputRef" class="hidden" />

    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div>
                <img
                    :src="workspace.logo_url"
                    :alt="workspace.name"
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
            v-if="workspace.media?.length >= 1"
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
