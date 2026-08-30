import type { Component } from 'vue';

export interface FilterOption {
    value: string;
    label: string;
    color?: string | null;
}

export interface FilterCategory {
    key: string;
    label: string;
    icon?: Component;
    options: FilterOption[];
}

/** Each category holds the values picked in it, so filters can combine. */
export type FilterSelection = Record<string, string[]>;
