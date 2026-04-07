<script setup lang="ts">
import { VisArea, VisAxis, VisGroupedBar, VisLine, VisXYContainer } from '@unovis/vue';
import { computed, ref } from 'vue';
import { formatNumberCompact } from '@/lib/utils';
import {
    type ChartConfig,
    ChartContainer,
    ChartCrosshair,
    ChartTooltip,
    ChartTooltipContent,
    componentToString,
} from '@/components/ui/chart';

const VIOLET = '#A78BFA';

const types = [
    { value: 'line', label: 'Line' },
    { value: 'bar', label: 'Bar' },
];

const props = defineProps<{
    type?: string;
    title: string;
    data: {
        total: number;
        chart: {
            labels: string[];
            data: number[];
            label: string;
        };
    };
}>();

const chartType = ref(types.find((t) => t.value === (props.type ?? 'bar')) ?? types[1]);

type DataPoint = { index: number; label: string; value: number };

const chartData = computed<DataPoint[]>(() =>
    props.data.chart.labels.map((label, i) => ({
        index: i,
        label,
        value: props.data.chart.data[i] ?? 0,
    }))
);

const xAccessor = (_d: DataPoint, i: number) => i;
const yAccessor = (d: DataPoint) => d.value;

const xTickFormat = (i: number) => {
    const items = chartData.value;
    if (i === 0) return items[0]?.label ?? '';
    if (i === items.length - 1) return items[items.length - 1]?.label ?? '';
    return '';
};

const chartConfig: ChartConfig = {
    value: { label: props.title, color: VIOLET },
};

const tooltipTemplate = componentToString(chartConfig, ChartTooltipContent, {
    indicator: 'dot' as const,
    labelFormatter: (x: number | Date) => chartData.value[x as number]?.label ?? '',
});
</script>

<template>
    <div class="border border-border rounded-lg overflow-hidden p-4">
        <div class="w-full flex justify-between mb-4">
            <div>
                <div class="text-base font-medium text-foreground mb-1">{{ title }}</div>
                <div class="text-2xl font-semibold text-foreground">{{ formatNumberCompact(data.total) }}</div>
            </div>
            <div class="flex justify-center">
                <fieldset>
                    <div class="grid grid-cols-2 gap-x-1 rounded-md p-1 text-center text-xs font-semibold leading-5 ring-1 ring-inset ring-border">
                        <button
                            v-for="option in types"
                            :key="option.value"
                            type="button"
                            :class="[
                                chartType.value === option.value
                                    ? 'bg-foreground text-background'
                                    : 'text-muted-foreground',
                                'cursor-pointer rounded px-2.5 py-1 transition-colors',
                            ]"
                            @click="chartType = option"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </fieldset>
            </div>
        </div>

        <ChartContainer :config="chartConfig" class="h-[260px]">
            <template #default>
                <VisXYContainer :data="chartData" :height="260">
                    <template v-if="chartType.value === 'line'">
                        <VisArea
                            :x="xAccessor"
                            :y="yAccessor"
                            :color="VIOLET"
                            :opacity="0.15"
                            curveType="monotoneX"
                        />
                        <VisLine
                            :x="xAccessor"
                            :y="yAccessor"
                            :color="VIOLET"
                            :lineWidth="1.5"
                            curveType="monotoneX"
                        />
                    </template>
                    <template v-else>
                        <VisGroupedBar
                            :x="xAccessor"
                            :y="[yAccessor]"
                            :color="[VIOLET]"
                            :barPadding="0.4"
                        />
                    </template>

                    <VisAxis
                        type="x"
                        :tickFormat="xTickFormat"
                        :numTicks="chartData.length"
                        :gridLine="false"
                        :domainLine="false"
                        :tickLine="false"
                    />

                    <ChartCrosshair
                        :template="tooltipTemplate"
                        color="var(--border)"
                    />
                    <ChartTooltip />
                </VisXYContainer>
            </template>
        </ChartContainer>
    </div>
</template>
