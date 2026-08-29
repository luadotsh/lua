<script setup lang="ts">
import mapboxgl from 'mapbox-gl';
import { usePage } from '@inertiajs/vue3';
import {
    computed,
    onBeforeUnmount,
    onMounted,
    ref,
    shallowRef,
    watch,
} from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import { countryFor } from '@/lib/countries';
import { formatCount } from '@/lib/metrics';
import type { BreakdownRow } from '@/components/analytics/BreakdownCard.vue';

import 'mapbox-gl/dist/mapbox-gl.css';

const props = defineProps<{
    rows: BreakdownRow[];
}>();

// Shared by HandleInertiaRequests from config/services.php, so MAPBOX_TOKEN
// in .env is all it takes. Without it the card explains itself instead of
// rendering an empty box.
const mapboxToken = computed(
    () => (usePage().props.mapboxToken as string | null) ?? null,
);

const mapContainer = ref<HTMLElement | null>(null);
const map = shallowRef<mapboxgl.Map | null>(null);
const styleReady = ref(false);

const { resolvedAppearance } = useAppearance();

const mapStyle = computed(() =>
    resolvedAppearance.value === 'dark'
        ? 'mapbox://styles/mapbox/dark-v11'
        : 'mapbox://styles/mapbox/light-v11',
);

const peak = computed(() => Math.max(1, ...props.rows.map((row) => row.events)));

const eventsByCode = computed<Record<string, number>>(() =>
    Object.fromEntries(props.rows.map((row) => [row.value.toUpperCase(), row.events])),
);

// Countries are shaded by how much of the traffic they hold, so the map reads
// as a distribution rather than as a set of pins.
const opacityExpression = computed(() => {
    if (props.rows.length === 0) {
        return 0;
    }

    const expression: unknown[] = ['match', ['get', 'iso_3166_1']];

    props.rows.forEach((row) => {
        expression.push(
            row.value.toUpperCase(),
            0.2 + (row.events / peak.value) * 0.65,
        );
    });

    expression.push(0);

    return expression as mapboxgl.Expression;
});

// The same violet the timeseries uses, so the two read as one chart. It is a
// literal rather than a CSS variable on purpose: --primary is a neutral in
// this theme (near-white on dark), which would flood the map, and Mapbox
// paints through WebGL and cannot resolve a custom property anyway.
const FILL = '#8b5cf6';

const applyData = () => {
    const instance = map.value;

    if (!instance || !styleReady.value) {
        return;
    }

    if (!instance.getSource('countries')) {
        instance.addSource('countries', {
            type: 'vector',
            url: 'mapbox://mapbox.country-boundaries-v1',
        });
    }

    if (!instance.getLayer('lua-country-fills')) {
        instance.addLayer({
            id: 'lua-country-fills',
            type: 'fill',
            source: 'countries',
            'source-layer': 'country_boundaries',
            filter: ['any', ['in', 'all', ['get', 'worldview']]],
            paint: {
                'fill-color': FILL,
                'fill-opacity': 0,
            },
        });
    }

    instance.setPaintProperty('lua-country-fills', 'fill-opacity', opacityExpression.value);
};

const bindInteractions = (instance: mapboxgl.Map) => {
    const popup = new mapboxgl.Popup({
        closeButton: false,
        closeOnClick: false,
        className: 'lua-map-popup',
    });

    instance.on('mousemove', 'lua-country-fills', (event) => {
        const code = (event.features?.[0]?.properties?.iso_3166_1 as string) ?? '';
        const events = eventsByCode.value[code];

        if (!events) {
            popup.remove();
            instance.getCanvas().style.cursor = '';

            return;
        }

        instance.getCanvas().style.cursor = 'default';

        popup
            .setLngLat(event.lngLat)
            .setHTML(
                `<strong>${countryFor(code).flag} ${countryFor(code).name}</strong><br>${formatCount(events)} events`,
            )
            .addTo(instance);
    });

    instance.on('mouseleave', 'lua-country-fills', () => {
        popup.remove();
        instance.getCanvas().style.cursor = '';
    });
};

const initializeMap = () => {
    if (!mapboxToken.value || !mapContainer.value || map.value) {
        return;
    }

    mapboxgl.accessToken = mapboxToken.value;

    const instance = new mapboxgl.Map({
        container: mapContainer.value,
        style: mapStyle.value,
        center: [0, 24],
        zoom: 0.3,
        projection: 'mercator',
        attributionControl: false,
        // Scrolling the page over the map should scroll the page.
        cooperativeGestures: true,
    });

    instance.on('style.load', () => {
        styleReady.value = true;
        applyData();
    });

    bindInteractions(instance);

    map.value = instance;
};

onMounted(initializeMap);

// The container sits behind a v-if while the data loads, so it may not exist
// at mount; initialise as soon as it enters the DOM.
watch(mapContainer, () => initializeMap());

watch(mapStyle, (style) => {
    styleReady.value = false;
    map.value?.setStyle(style);
});

watch(() => props.rows, () => applyData());

onBeforeUnmount(() => {
    map.value?.remove();
    map.value = null;
});
</script>

<template>
    <section class="flex flex-col rounded-lg border border-border bg-card">
        <header class="flex items-center justify-between gap-4 border-b border-border px-4 py-3">
            <h2 class="text-sm font-medium text-foreground">Where clicks come from</h2>
            <span class="text-xs tabular-nums text-muted-foreground">
                {{ rows.length }} {{ rows.length === 1 ? 'country' : 'countries' }}
            </span>
        </header>

        <p
            v-if="!mapboxToken"
            class="px-4 py-12 text-center text-sm text-muted-foreground"
        >
            Set <code class="font-mono text-xs">MAPBOX_TOKEN</code> to show the map.
        </p>

        <div
            v-else
            ref="mapContainer"
            class="h-80 w-full overflow-hidden rounded-b-lg"
            data-test="visitors-map"
        />
    </section>
</template>

<style scoped>
:deep(.mapboxgl-ctrl-logo) {
    display: none !important;
}

/* Mapbox appends popups as plain DOM nodes inside the container, so :deep
   reaches them. Its default popup is a hardcoded white card, illegible on a
   dark map — repaint it with the app's own tokens so it follows the theme. */
:deep(.lua-map-popup .mapboxgl-popup-content) {
    background: var(--popover);
    color: var(--popover-foreground);
    box-shadow: none;
    border: 1px solid var(--border);
    font-size: 0.8rem;
}

:deep(.lua-map-popup .mapboxgl-popup-tip) {
    border-top-color: var(--popover);
    border-bottom-color: var(--popover);
    border-left-color: var(--popover);
    border-right-color: var(--popover);
}
</style>
