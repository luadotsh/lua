<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { IconTrash } from '@tabler/icons-vue';
import axios from 'axios';
import { computed, ref } from 'vue';

import { Avatar } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import * as mediasRoutes from '@/routes/medias';

const inputRef = ref<HTMLInputElement | null>(null);
const uploadHasErrors = ref<string | null>(null);
const isLoading = ref(false);

const workspace = computed(() => usePage().props.auth.user.current_workspace);

const upload = async () => {
    if (!workspace.value) {
        return;
    }

    inputRef.value!.click();

    inputRef.value!.onchange = async (event: Event) => {
        isLoading.value = true;

        const formData = new FormData();
        formData.append('media', (event.target as HTMLInputElement).files![0]);
        formData.append('collection', 'logo');

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
                inputRef.value!.value = '';
            });
    };
};

const destroy = () => {
    const media = workspace.value?.media?.[0];

    if (!media) {
        return;
    }

    router.delete(mediasRoutes.destroy.url(media.id), {
        onSuccess: () => {
            router.reload();
        },
    });
};
</script>

<template>
    <input type="file" ref="inputRef" class="hidden" />

    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div>
                <Avatar
                    :src="workspace?.logo_url"
                    :name="workspace?.name ?? '?'"
                    class="size-10 rounded-full"
                    fallback-class="bg-violet-100 font-bold text-violet-700"
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
            v-if="(workspace?.media?.length ?? 0) >= 1"
            @click="destroy"
            class="cursor-pointer rounded-md p-2 hover:bg-zinc-100"
        >
            <IconTrash class="h-5 w-5 text-zinc-500" />
        </div>
    </div>
    <div v-show="uploadHasErrors" class="my-2">
        <p class="text-sm text-red-600">
            {{ uploadHasErrors }}
        </p>
    </div>
</template>
