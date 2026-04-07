<script setup lang="ts">
import { watch, ref } from 'vue';
import country from '@/country';
import { calcTotal, calcPercentage, kFormatter, favicon } from '@/lib/utils';
import { favicon as websitesFaviconRoute } from '@/routes/websites';

type DataItem = { x: string; y: number };

const prop = defineProps<{
    data: DataItem[];
    favicon?: boolean;
    country?: boolean;
    capitalize?: boolean;
    uppercase?: boolean;
}>();

const total = ref(0);

const formatName = (name: string) => {
    return prop.country ? country.getCountryName(name) : name;
};

const getFaviconUrl = (url: string) => {
    try {
        return websitesFaviconRoute({ query: { url: encodeURI(url) } }).url;
    } catch {
        return favicon(url);
    }
};

watch(
    () => prop.data,
    () => {
        total.value = calcTotal(prop.data);
    },
    { deep: true, immediate: true },
);
</script>

<template>
    <div v-if="prop.data.length >= 1">
        <div class="space-y-2 relative">
            <div
                v-for="value in prop.data"
                :key="value.x"
                class="relative w-full group"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div
                            class="absolute top-0 left-0 h-full rounded-r-md bg-zinc-100 dark:bg-zinc-500/20"
                            :style="`max-width: 85%; width: ${calcPercentage(total, value.y)}%`"
                        />
                        <div class="flex items-center space-x-2 z-10 px-2 py-1">
                            <img
                                v-if="prop.favicon"
                                :src="getFaviconUrl(value.x)"
                                class="w-4 h-4"
                                :alt="value.x"
                            />
                            <div v-if="prop.country">
                                {{ country.getCountryFlag(value.x) }}
                            </div>
                            <div
                                :class="{
                                    'text-foreground text-sm font-medium': true,
                                    capitalize: prop.capitalize,
                                    uppercase: prop.uppercase,
                                }"
                            >
                                {{ formatName(value.x) }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between min-w-[3rem]">
                        <div class="invisible group-hover:visible flex text-xs font-semibold text-muted-foreground mr-2">
                            {{ calcPercentage(total, value.y) }}%
                        </div>
                        <div class="text-sm font-semibold text-foreground">
                            {{ kFormatter(value.y) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="min-h-[300px] flex flex-col sm:justify-center items-center">
        <div class="text-muted-foreground font-medium text-sm">No data</div>
    </div>
</template>
