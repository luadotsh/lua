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
});

const listboxButton = ref(null);
const dropdownPosition = ref({ top: "0px", left: "0px", width: "0px" });

const input = ref(null);
const query = ref("");
const selected = ref(
    multiple
        ? options.filter((o) => modelValue.includes(o.id))
        : options.find((o) => o.id === modelValue)
);

const o = [...options];

const filtered = computed(() => {
    return query.value === ""
        ? o
        : o.filter((op) => {
              return op.label.toLowerCase().includes(query.value.toLowerCase());
          });
});

const setFocus = (open) => {
    if (open) {
        setTimeout(() => {
            input.value?.focus();
        }, 200);
    }
};

const updateDropdownPosition = () => {
    if (listboxButton.value) {
        const buttonRect = listboxButton.value.getBoundingClientRect();
        dropdownPosition.value = {
            top: `${buttonRect.bottom + window.scrollY}px`,
            left: `${buttonRect.left + window.scrollX}px`,
            width: `${buttonRect.width}px`,
        };
    }
};

watch(
    () => selected.value,
    (value) => {
        updateDropdownPosition();
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

watch(
    () => query.value,
    () => {
        updateDropdownPosition();
    }
);
</script>

<template>
    <Listbox as="div" v-model="selected" :multiple="multiple" v-slot="{ open }">
        <div class="relative">
            <div ref="listboxButton">
                <ListboxButton
                    @click="
                        setFocus(!open);
                        updateDropdownPosition();
                    "
                    :class="{
                        'rounded-md w-full border border-zinc-150 dark:border-zinc-700 dark:bg-zinc-900 py-2 px-3 text-left text-black dark:text-zinc-300 focus:outline-none text-sm':
                            button,
                        'flex items-center cursor-pointer disabled:cursor-not-allowed relative': true,
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

                        <div
                            v-if="selected.color"
                            :class="`h-2 w-2 rounded-full bg-${selected.color}-400`"
                        />

                        <div>
                            {{ selected.label }}
                        </div>
                    </div>

                    <div class="block truncate" v-else>{{ placeholder }}</div>
                    <div class="pointer-events-none flex items-center ml-auto">
                        <PhCaretUp
                            v-if="open"
                            class="h-5 w-5 text-zinc-900 dark:text-zinc-400 stroke-2"
                            aria-hidden="true"
                            weight="duotone"
                        />
                        <PhCaretDown
                            v-else
                            class="h-5 w-5 text-zinc-900 dark:text-zinc-400 stroke-2"
                            aria-hidden="true"
                            weight="duotone"
                        />
                    </div>
                </ListboxButton>
            </div>
            <teleport to="body">
                <transition
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <ListboxOptions
                        class="absolute z-50 mt-1 w-full rounded-lg bg-white dark:bg-zinc-900 shadow-2xl border border-zinc-150 dark:border-zinc-800 focus:outline-none text-sm"
                        :style="dropdownPosition"
                    >
                        <div
                            v-if="search"
                            class="relative border-b border-zinc-150 dark:border-zinc-800 rounded-t overflow-hidden"
                        >
                            <div class="relative px-1 py-1">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
                                >
                                    <PhMagnifyingGlass
                                        weight="duotone"
                                        class="h-5 w-5 text-zinc-400 dark:text-zinc-400 stroke-2"
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

                                                <div
                                                    v-if="option.color"
                                                    :class="`h-2 w-2 rounded-full bg-${option.color}-400`"
                                                />

                                                <div>
                                                    {{ option.label }}
                                                </div>
                                            </div>

                                            <PhCheck
                                                v-if="selected || active"
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
            </teleport>
        </div>
    </Listbox>
</template>
