<script setup>
import { onMounted, ref } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

import { PhX, PhPlus, PhCaretDown } from "@phosphor-icons/vue";

import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

const tags = usePage().props.tags;

const { form } = defineProps({
    form: Object,
});

const add = (tag) => {
    if (!tag.selected) {
        tag.selected = true;
        form.tags.push(tag);
    }
};

const remove = (tag) => {
    for (const i in tags) {
        if (tags[i].id == tag.id) {
            tags[i].selected = false;
        }
    }

    // varre o array de selecionados para remover
    for (const i in form.tags) {
        if (form.tags[i].id == tag.id) {
            form.tags.splice(i, 1);
        }
    }
};

onMounted(() => {
    for (const i in tags) {
        tags[i].selected = false;
    }

    tags.map((tag) => {
        form.tags.map((t) => {
            if (tag.id == t.id) {
                tag.selected = true;
            }
        });
    });
});
</script>
<template>
    <div class="">
        <div v-if="form.tags.length >= 1" class="mb-2">
            <div class="inline-flex" v-for="tag in form.tags" :key="tag">
                <div
                    :class="`mr-1 flex items-center text-xs font-medium px-1.5 py-0.5 rounded text-white bg-${tag.color}-500`"
                >
                    <div>{{ tag.name }}</div>
                    <div @click="remove(tag)">
                        <PhX
                            class="h-3.5 w-3.5 stroke-2 ml-1 hover:opacity-75 cursor-pointer"
                            aria-hidden="true"
                        />
                    </div>
                </div>
            </div>
        </div>

        <Menu as="div" class="relative inline-block text-left btn-sm w-full">
            <div>
                <MenuButton class="btn btn-secondary w-full !justify-between">
                    <div>Add Tag</div>

                    <PhCaretDown
                        class="-mr-1 h-4 w-4 text-gray-400"
                        aria-hidden="true"
                    />
                </MenuButton>
            </div>

            <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <MenuItems
                    class="absolute right-0 z-10 mt-2 w-full divide-y divide-gray-100 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                    <div class="py-1">
                        <MenuItem
                            v-for="tag in tags"
                            :key="tag"
                            v-slot="{ active }"
                        >
                            <div
                                @click="add(tag)"
                                :class="[
                                    tag.selected
                                        ? 'bg-gray-100 text-gray-900 opacity-30'
                                        : 'text-gray-700 hover:bg-gray-100',
                                    'group flex items-center px-4 py-2 text-sm cursor-pointer',
                                ]"
                            >
                                <div
                                    :class="`mr-2 h-2 w-2 rounded-full bg-${tag.color}-500`"
                                ></div>

                                <div>
                                    {{ tag.name }}
                                </div>
                            </div>
                        </MenuItem>
                    </div>

                    <div class="py-1">
                        <MenuItem v-slot="{ active }">
                            <Link
                                :href="route('setting.tags.index')"
                                class="text-gray-700 hover:bg-gray-100 group flex items-center px-4 py-2 text-sm cursor-pointer space-x-2 font-medium"
                            >
                                <PhPlus class="h-4 w-4" aria-hidden="true" />
                                <div>Create Tag</div>
                            </Link>
                        </MenuItem>
                    </div>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>
