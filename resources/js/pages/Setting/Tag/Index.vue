<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { IconPencil, IconTrash } from "@tabler/icons-vue";
import { ref } from "vue";
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
const tagToDelete = ref<Tag | null>(null);

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

    <CreateModal ref="createModal" />
    <EditModal ref="editModal" />

    <AlertDialog
        :open="!!tagToDelete"
        @update:open="(open) => !open && (tagToDelete = null)"
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete tag</AlertDialogTitle>
                <AlertDialogDescription>
                    Links carrying <strong>{{ tagToDelete?.name }}</strong> keep
                    working; they simply lose the tag.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogAction
                    class="bg-destructive text-white hover:bg-destructive/90"
                    @click="deleteTag"
                >
                    Delete
                </AlertDialogAction>
                <AlertDialogCancel @click="tagToDelete = null">Cancel</AlertDialogCancel>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>

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
                                                @click="tagToDelete = tag"
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
