<script setup lang="ts">
import { ref, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import { Button } from "@/components/ui/button";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { IconX, IconSettings, IconGripVertical } from "@tabler/icons-vue";
import AppLayout from "@/layouts/AppLayout.vue";
import SettingsLayout from "@/layouts/settings/Layout.vue";
import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";
import * as tagsRoutes from "@/routes/setting/tags";
import axios from "axios";

const createModal = ref<InstanceType<typeof CreateModal> | null>(null);
const editModal = ref<InstanceType<typeof EditModal> | null>(null);
const tagToDelete = ref<{ id: string | number } | null>(null);

const props = defineProps<{
    tags: object;
}>();

const list = ref(props.tags);

const updateOrder = () => {
    axios.post(tagsRoutes.sort.url(), {
        tags: list.value,
    });
};

watch(
    () => props.tags,
    (value) => {
        list.value = value;
    }
);

const confirmDelete = (tag: { id: string | number }) => {
    tagToDelete.value = tag;
};

const deleteTag = () => {
    if (!tagToDelete.value) {
        return;
    }

    router.delete(tagsRoutes.destroy.url(tagToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            tagToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Tags" />

    <AppLayout>
        <SettingsLayout>
            <CreateModal ref="createModal" />
            <EditModal ref="editModal" />

            <AlertDialog :open="!!tagToDelete" @update:open="(val) => !val && (tagToDelete = null)">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Delete Tag</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to delete this tag?
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="tagToDelete = null">Cancel</AlertDialogCancel>
                        <AlertDialogAction @click="deleteTag" class="bg-red-600 hover:bg-red-700">Delete</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold">Tags</h2>
                <Button @click="createModal?.open()">
                    New Tag
                </Button>
            </div>

            <div>
                <draggable
                    @end="updateOrder"
                    v-model="list"
                    ghost-class="opacity-30"
                    tag="div"
                    item-key="id"
                    class="px-4 sm:px-0 space-y-2"
                >
                    <template #item="{ element }">
                        <div class="flex flex-1 items-center space-x-1">
                            <div class="flex-none">
                                <IconGripVertical
                                    class="text-zinc-800 dark:text-zinc-300 cursor-move h-5 w-5"
                                />
                            </div>
                            <div
                                class="flex flex-1 items-center justify-between rounded-md px-4 py-2 border border-zinc-100 dark:border-zinc-700"
                            >
                                <div class="flex flex-1 items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            :class="`h-2 w-2 rounded-full bg-${element.color}-400`"
                                        ></div>
                                        <div
                                            class="font-medium text-sm text-zinc-600 dark:text-white"
                                        >
                                            {{ element.name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="space-x-1"
                                        @click="editModal?.open(element)"
                                    >
                                        <IconSettings class="h-3 w-3" />
                                        <span>Edit</span>
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="space-x-1"
                                        @click="confirmDelete(element)"
                                    >
                                        <IconX class="h-3 w-3" />
                                        <span>Delete</span>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </template>
                </draggable>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
