<script setup>
import { ref, computed } from "vue";
import { RadioGroup, RadioGroupOption } from "@headlessui/vue";

const emit = defineEmits(["update"]);

const props = defineProps({
    tabs: Array,
    title: String,
    currentTab: String,
});

const setCurrentTab = (tab) => {
    emit("update", tab);
};

const maxGridClass = computed(() => {
    return {
        [`grid-cols-${props.tabs.length}`]:
            props.tabs.length >= 2 && props.tabs.length <= 6,
    };
});
</script>

<template>
    <div class="flex items-center justify-between">
        <div
            class="capitalize text-base font-semibold text-black dark:text-white"
        >
            {{ title }}
        </div>

        <div class="flex-none">
            <div class="flex items-center space-x-2">
                <slot v-if="$slots.left" name="left" />
                <template v-if="props.tabs.length >= 2">
                    <fieldset class="w-full">
                        <RadioGroup
                            v-model="props.currentTab"
                            class="flex space-x-1 rounded-md p-1 text-center text-xs font-semibold ring-1 ring-inset ring-zinc-200 dark:ring-zinc-700"
                            :class="maxGridClass"
                        >
                            <RadioGroupOption
                                as="template"
                                v-for="tab in props.tabs"
                                :key="tab.value"
                                :value="tab"
                            >
                                <div
                                    @click="setCurrentTab(tab)"
                                    :class="[
                                        currentTab == tab
                                            ? 'bg-zinc-800 dark:bg-zinc-900 text-white'
                                            : 'text-zinc-500 dark:text-zinc-300',
                                        'cursor-pointer rounded px-2.5 py-1',
                                    ]"
                                >
                                    {{ tab }}
                                </div>
                            </RadioGroupOption>
                        </RadioGroup>
                    </fieldset>
                </template>
            </div>
        </div>
    </div>
</template>
