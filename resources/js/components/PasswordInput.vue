<script setup lang="ts">
import { IconEye, IconEyeOff } from '@tabler/icons-vue';
import type { HTMLAttributes } from 'vue';
import { ref } from 'vue';

import { Input } from '@/components/ui/input';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

const props = defineProps<{
    id?: string;
    autocomplete?: string;
    placeholder?: string;
    autofocus?: boolean;
    class?: HTMLAttributes['class'];
}>();

const modelValue = defineModel<string>({ default: '' });

const showPassword = ref(false);
</script>

<template>
    <div class="relative">
        <Input
            :id="id"
            v-model="modelValue"
            :type="showPassword ? 'text' : 'password'"
            :autocomplete="autocomplete"
            :placeholder="placeholder"
            :autofocus="autofocus"
            :class="cn('pe-9', props.class)"
        />

        <div class="absolute inset-y-0 end-0 flex items-center pe-3">
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger as-child>
                        <button
                            type="button"
                            :tabindex="-1"
                            :aria-label="
                                showPassword ? 'Hide password' : 'Show password'
                            "
                            class="cursor-pointer text-muted-foreground hover:text-foreground"
                            @click="showPassword = !showPassword"
                        >
                            <IconEyeOff v-if="showPassword" class="size-4" />
                            <IconEye v-else class="size-4" />
                        </button>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>
                            {{
                                showPassword ? 'Hide password' : 'Show password'
                            }}
                        </p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>
    </div>
</template>
