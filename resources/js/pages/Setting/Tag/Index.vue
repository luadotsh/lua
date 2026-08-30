<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { IconPencil, IconTrash } from "@tabler/icons-vue";
import { ref } from "vue";
import ConfirmDeleteModal, {
    DELETE_KEYWORD,
} from "@/components/ConfirmDeleteModal.vue";
import { Button } from "@/components/ui/button";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import date from "@/date";
import AppLayout from "@/layouts/AppLayout.vue";
import * as tagsRoutes from "@/routes/setting/tags";
import type { Tag } from "@/types";
import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";

const props = defineProps<{
    tags: Tag[];
}>();

const createModal = ref<InstanceType<typeof CreateModal> | null>(null);
const editModal = ref<InstanceType<typeof EditModal> | null>(null);
const deleteModal = ref<InstanceType<typeof ConfirmDeleteModal> | null>(null);

const confirmDelete = (tag: Tag) => {
    deleteModal.value?.open({
        url: tagsRoutes.destroy.url(tag.id),
        confirmText: DELETE_KEYWORD,
    });
};
</script>

<template>
    <Head title="Tags" />

    <CreateModal ref="createModal" />
    <EditModal ref="editModal" />

    <ConfirmDeleteModal
        ref="deleteModal"
        title="Delete tag"
        description="Links carrying this tag keep working; they simply lose the tag."
    />

    <AppLayout title="Tags" :total="tags.length" full-width>
        <template #header-actions>
            <Button @click="createModal?.open()">New Tag</Button>
        </template>

        <div class="flex min-h-0 min-w-0 flex-1 flex-col">
            <div
                class="min-h-0 min-w-0 flex-1 overflow-auto pb-px"
                data-testid="tags-scroll"
            >
                <Table>
                    <TableHeader sticky>
                        <TableRow>
                            <TableHead class="w-full">Name</TableHead>
                            <TableHead class="w-px whitespace-nowrap">Created</TableHead>
                            <TableHead class="w-px">
                                <span class="sr-only">Actions</span>
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody id="tags-body">
                        <TableRow v-for="tag in tags" :key="tag.id">
                            <TableCell>
                                <span class="flex items-center gap-2">
                                    <span
                                        class="size-2.5 shrink-0 rounded-full"
                                        :style="{ backgroundColor: tag.color }"
                                    />
                                    <span class="font-medium">{{ tag.name }}</span>
                                </span>
                            </TableCell>

                            <TableCell class="w-px whitespace-nowrap text-muted-foreground">
                                {{ date.diffForHumans(tag.created_at) }}
                            </TableCell>

                            <TableCell class="w-px">
                                <div class="flex items-center justify-end gap-1">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="size-8"
                                                :data-testid="`tag-edit-${tag.id}`"
                                                @click="editModal?.open(tag)"
                                            >
                                                <IconPencil class="size-3.5" />
                                                <span class="sr-only">Edit</span>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>Edit</TooltipContent>
                                    </Tooltip>

                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="size-8 text-muted-foreground hover:text-destructive"
                                                :data-testid="`tag-delete-${tag.id}`"
                                                @click="confirmDelete(tag)"
                                            >
                                                <IconTrash class="size-3.5" />
                                                <span class="sr-only">Delete</span>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>Delete</TooltipContent>
                                    </Tooltip>
                                </div>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="tags.length === 0">
                            <TableCell
                                colspan="3"
                                class="py-10 text-center text-muted-foreground"
                            >
                                No tags yet. Create one to group your links.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
