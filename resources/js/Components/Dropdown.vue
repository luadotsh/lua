<script setup>
import { ref, watch, computed } from "vue";
import {
    Listbox,
    ListboxButton,
    ListboxOption,
    ListboxOptions,
} from "@headlessui/vue";

import {
    PhCaretDown,
    PhCaretUp,
    PhMagnifyingGlass,
    PhCheck,
} from "@phosphor-icons/vue";

const emit = defineEmits(["update:modelValue"]);

const { modelValue, options, placeholder, multiple } = defineProps({
    modelValue: {
        type: [String, Number, Boolean, Array],
        required: false,
    },

    multiple: {
        type: Boolean,
        default: false,
    },

    options: {
        type: Array,
        required: true,
    },

    placeholder: {
        type: String,
        default: "Select...",
    },

    search: {
        type: Boolean,
        default: false,
    },

    button: {
        type: Boolean,
        default: true,
    },
    direction: {
        type: String,
        default: "down", // Accepts "up" or "down"
    },
});

const input = ref(null);
const query = ref("");
const selected = ref(
    multiple
        ? options.filter((o) => modelValue.includes(o.id))
        : options.find((o) => o.id === modelValue)
);

const filtered = computed(() =>
    query.value === ""
        ? options
        : options.filter((option) => {
              return option.label
                  .toLowerCase()
                  .includes(query.value.toLowerCase());
          })
);

const setFocus = (open) => {
    if (open) {
        setTimeout(() => {
            input.value?.focus();
        }, 200);
    }
};

watch(
    () => selected.value,
    (value) => {
        if (multiple) {
            emit(
                "update:modelValue",
                value.map((o) => o.id)
            );
            return;
        }

        emit("update:modelValue", value.id);
        query.value = "";
    }
);
</script>

<template>
    <Listbox as="div" v-model="selected" :multiple="multiple" v-slot="{ open }">
        <div class="relative">
            <ListboxButton
                @click="setFocus(!open)"
                :class="{
                    'form-input': button,
                    'flex items-center cursor-pointer disabled:cursor-not-allowed relative w-full': true,
                }"
            >
                <div
                    class="block truncate"
                    v-if="selected && multiple && selected.length >= 1"
                >
                    {{ selected.map((o) => o.label).join(", ") }}
                </div>

                <div
                    class="flex items-center space-x-2 truncate font-normal"
                    v-else-if="selected && !multiple"
                >
                    <img
                        v-if="selected.image"
                        :src="selected.image"
                        :alt="selected.label"
                        class="h-5 w-5 rounded-full"
                    />
                    <div>
                        {{ selected.label }}
                    </div>
                </div>

                <div class="block truncate" v-else>{{ placeholder }}</div>
                <div class="pointer-events-none flex items-center ml-auto">
                    <div class="pointer-events-none flex items-center ml-auto">
                        <PhCaretUp
                            v-if="
                                (direction === 'down' && open) ||
                                (direction === 'up' && !open)
                            "
                            class="h-5 w-5 text-zinc-900 dark:text-zinc-400 stroke-2"
                            aria-hidden="true"
                            weight="fill"
                        />
                        <PhCaretDown
                            v-else
                            class="h-5 w-5 text-zinc-900 dark:text-zinc-400 stroke-2"
                            aria-hidden="true"
                            weight="fill"
                        />
                    </div>
                </div>
            </ListboxButton>

            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    :class="{
                        'absolute z-50 w-full rounded-md bg-white dark:bg-zinc-900 shadow-2xl border border-zinc-300 dark:border-zinc-800 focus:outline-none text-sm': true,
                        'mt-1': direction === 'down',
                        'bottom-full mb-1': direction === 'up',
                    }"
                >
                    <div
                        v-if="search"
                        class="relative border-b border-zinc-300 dark:border-zinc-800 rounded-t overflow-hidden"
                    >
                        <div class="relative px-1 py-1">
                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
                            >
                                <PhMagnifyingGlass
                                    weight="fill"
                                    class="h-5 w-5 text-zinc-900 dark:text-zinc-400 stroke-2"
                                />
                            </div>
                            <input
                                v-model="query"
                                type="text"
                                ref="input"
                                :placeholder="placeholder"
                                class="w-full pl-10 text-sm outline-none placeholder:text-zinc-500 dark:text-zinc-300 focus:ring-0 border-0 bg-white dark:bg-zinc-900"
                            />
                        </div>
                    </div>
                    <div class="max-h-48 overflow-y-auto">
                        <div class="p-1 space-y-0.5">
                            <template v-if="filtered.length > 0">
                                <ListboxOption
                                    as="template"
                                    v-for="option in filtered"
                                    :key="option.id"
                                    :value="option"
                                    v-slot="{ active, selected }"
                                    @click="query = ''"
                                >
                                    <li
                                        :class="[
                                            active || selected
                                                ? 'bg-zinc-100 dark:bg-zinc-800 '
                                                : '',
                                            'text-sm text-zinc-900 dark:text-zinc-400 relative cursor-pointer select-none py-1.5 px-3 rounded-md flex items-center justify-between',
                                        ]"
                                    >
                                        <div
                                            :class="[
                                                selected
                                                    ? ' text-zinc-900 dark:text-white'
                                                    : ' ',
                                                'flex items-center space-x-2 truncate font-normal',
                                            ]"
                                        >
                                            <img
                                                v-if="option.image"
                                                :src="option.image"
                                                :alt="option.label"
                                                class="h-5 w-5 rounded-full"
                                            />
                                            <div>
                                                {{ option.label }}
                                            </div>
                                        </div>

                                        <PhCheck
                                            v-if="selected"
                                            weight="bold"
                                            class="h-4 w-4 text-zinc-900 dark:text-green-500 stroke-2"
                                        />
                                    </li>
                                </ListboxOption>
                            </template>

                            <template v-else>
                                <li
                                    class="text-sm text-zinc-900 dark:text-zinc-400 py-3 px-3"
                                >
                                    No results found
                                </li>
                            </template>
                        </div>
                    </div>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>
