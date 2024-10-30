<script setup>
import { ref, onMounted, watch } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import Button from "@/Components/Button.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";

import { PhX, PhGear, PhDotsSixVertical } from "@phosphor-icons/vue";

import AppLayout from "@/Layouts/Master.vue";
import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";

const confirmDeleteModal = ref(null);
const createModal = ref(null);
const editModal = ref(null);

const props = defineProps({
    tags: Object,
});

const list = ref(props.tags);

const updateOrder = () => {
    axios
        .post(route("setting.tags.sort"), {
            tags: list.value,
        })
        .then((response) => {});
};

watch(
    () => props.tags,
    (value) => {
        list.value = value;
    }
);
</script>

<template>
    <Head title="Tags" />

    <AppLayout>
        <CreateModal ref="createModal" />
        <EditModal ref="editModal" />

        <ConfirmDeleteModal
            ref="confirmDeleteModal"
            description="Are you sure you want to delete this tag?"
        />

        <template #header>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <div class="sm:flex-auto">
                        <h1 class="page-title">Tags</h1>
                    </div>
                    <div>
                        <Button class="btn-primary" @click="createModal.open()">
                            New Tag
                        </Button>
                    </div>
                </div>
            </div>
        </template>

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
                            <PhDotsSixVertical
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
                                    class="btn-secondary btn-xs space-x-1"
                                    @click="editModal.open(element)"
                                >
                                    <PhGear class="h-3 w-3 stroke-2" />
                                    <div>Edit</div>
                                </Button>
                                <Button
                                    class="btn-secondary btn-xs space-x-1"
                                    @click="
                                        confirmDeleteModal.open({
                                            url: route('setting.tags.destroy', {
                                                id: element.id,
                                            }),
                                        })
                                    "
                                >
                                    <PhX class="h-3 w-3 stroke-2" />
                                    <div>Delete</div>
                                </Button>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>
    </AppLayout>
</template>
