<template>
    <SwitchGroup
        as="div"
        :class="{
            flex: true,
            'items-center': !description,
            'items-start': description,
        }"
    >
        <SwitchLabel
            v-if="orientation == 'right'"
            as="div"
            class="mr-auto flex flex-col"
        >
            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ label }}
            </div>
            <div
                v-if="description"
                class="text-xs font-medium text-zinc-400 dark:text-zinc-500"
            >
                {{ description }}
            </div>
        </SwitchLabel>
        <Switch
            :value="value"
            v-model="proxyChecked"
            @click="
                (e) => {
                    disabled ? e.preventDefault() : '';
                }
            "
            :class="[
                proxyChecked
                    ? 'border-transparent bg-black dark:bg-violet-600'
                    : 'bg-white dark:bg-zinc-900 border-black dark:border-zinc-700',
                ' relative inline-flex items-center flex-shrink-0 h-6 w-11 border rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none',
            ]"
        >
            <span
                aria-hidden="true"
                :class="[
                    proxyChecked
                        ? 'translate-x-5 bg-white'
                        : 'bg-black dark:bg-zinc-700 translate-x-0.5',
                    'pointer-events-none inline-block h-5 w-5 rounded-full shadow transform ring-0 transition ease-in-out duration-200',
                ]"
            />
        </Switch>
        <SwitchLabel
            v-if="orientation == 'left'"
            as="div"
            class="ml-2 flex flex-col"
        >
            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ label }}
            </div>
            <div
                v-if="description"
                class="text-xs font-medium text-zinc-400 dark:text-zinc-500"
            >
                {{ description }}
            </div>
        </SwitchLabel>
    </SwitchGroup>
</template>

<script>
import { Switch, SwitchGroup, SwitchLabel } from "@headlessui/vue";

export default {
    emits: ["update:checked"],

    props: {
        checked: {
            type: [Array, Boolean],
            default: false,
        },
        value: {
            default: null,
        },

        label: {
            default: null,
        },

        orientation: {
            default: "left",
            required: false,
        },

        description: {
            default: null,
        },

        disabled: {
            default: false,
            required: false,
        },
    },
    components: {
        Switch,
        SwitchGroup,
        SwitchLabel,
    },

    computed: {
        proxyChecked: {
            get() {
                return this.checked;
            },

            set(val) {
                this.$emit("update:checked", val);
            },
        },
    },
};
</script>
