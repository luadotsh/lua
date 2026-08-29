<script setup lang="ts">
import {
    IconBrandAndroid,
    IconBrandApple,
    IconClockExclamation,
    IconClockPause,
    IconLock,
    IconTargetArrow,
} from '@tabler/icons-vue';
import { computed } from 'vue';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import date from '@/date';

/**
 * What is configured on a link that the row would otherwise hide: a password
 * gate, an expiry, a platform-specific destination, a campaign. All of it was
 * already in the payload and none of it was visible.
 */
const props = defineProps<{
    hasPassword?: boolean;
    expiresAt?: string | null;
    ios?: string | null;
    android?: string | null;
    utmSource?: string | null;
    utmMedium?: string | null;
    utmCampaign?: string | null;
    utmTerm?: string | null;
    utmContent?: string | null;
}>();

const isExpired = computed(
    () => Boolean(props.expiresAt) && new Date(props.expiresAt as string) < new Date(),
);

const campaign = computed(() =>
    [
        props.utmSource && `source: ${props.utmSource}`,
        props.utmMedium && `medium: ${props.utmMedium}`,
        props.utmCampaign && `campaign: ${props.utmCampaign}`,
        props.utmTerm && `term: ${props.utmTerm}`,
        props.utmContent && `content: ${props.utmContent}`,
    ].filter(Boolean).join(' · '),
);
</script>

<template>
    <span class="flex items-center gap-1">
        <Tooltip v-if="hasPassword">
            <TooltipTrigger as-child>
                <IconLock class="size-3.5 text-muted-foreground" />
            </TooltipTrigger>
            <TooltipContent>Password protected</TooltipContent>
        </Tooltip>

        <Tooltip v-if="expiresAt">
            <TooltipTrigger as-child>
                <component
                    :is="isExpired ? IconClockExclamation : IconClockPause"
                    :class="isExpired ? 'size-3.5 text-destructive' : 'size-3.5 text-muted-foreground'"
                />
            </TooltipTrigger>
            <TooltipContent>
                {{ isExpired ? 'Expired' : 'Expires' }} {{ date.formatDateTime(expiresAt) }}
            </TooltipContent>
        </Tooltip>

        <Tooltip v-if="ios">
            <TooltipTrigger as-child>
                <IconBrandApple class="size-3.5 text-muted-foreground" />
            </TooltipTrigger>
            <TooltipContent>iOS visitors go to {{ ios }}</TooltipContent>
        </Tooltip>

        <Tooltip v-if="android">
            <TooltipTrigger as-child>
                <IconBrandAndroid class="size-3.5 text-muted-foreground" />
            </TooltipTrigger>
            <TooltipContent>Android visitors go to {{ android }}</TooltipContent>
        </Tooltip>

        <Tooltip v-if="campaign">
            <TooltipTrigger as-child>
                <IconTargetArrow class="size-3.5 text-muted-foreground" />
            </TooltipTrigger>
            <TooltipContent>{{ campaign }}</TooltipContent>
        </Tooltip>
    </span>
</template>
