<script setup>
import { onMounted, ref } from "vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

import { PhCaretDown } from "@phosphor-icons/vue";

const { title, description, isOpen } = defineProps({
    title: {
        type: String,
        required: false,
    },
    description: {
        type: String,
        required: false,
    },

    isOpen: {
        type: Boolean,
        default: false,
    },
});

const accordion = ref(null);
const open = ref(isOpen);

// when open the accordion, scroll this component to the bottom
const toggle = () => {
    open.value = !open.value;
};
</script>

<template>
    <div ref="accordion">
        <Disclosure :defaultOpen="isOpen">
            <DisclosureButton
                :class="{
                    'w-full': true,
                    'rounded-lg group bg-zinc-100 dark:bg-zinc-900/50 hover:border-zinc-500 cursor-pointer text-sm': true,
                }"
                @click="toggle"
            >
                <div
                    class="flex items-center justify-between space-x-2 px-4 py-2.5"
                >
                    <div
                        class="font-medium text-black dark:text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-white"
                    >
                        <slot name="title" />
                    </div>
                    <PhCaretDown
                        :class="{
                            'h-5 w-5 text-zinc-500 dark:text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-white flex-none stroke-2': true,
                            '-rotate-0': !open,
                            'rotate-180 transform': open,
                        }"
                    />
                </div>
            </DisclosureButton>
            <transition
                enter-active-class="transition-all ease-in-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-all ease-in-out duration-300"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-show="open">
                    <DisclosurePanel static>
                        <div
                            :class="{
                                'grid grid-cols-6 gap-x-6 gap-y-4 p-4': true,
                            }"
                        >
                            <slot name="content" />
                        </div>
                    </DisclosurePanel>
                </div>
            </transition>
        </Disclosure>
    </div>
</template>
