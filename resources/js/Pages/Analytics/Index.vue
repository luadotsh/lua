<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, computed, reactive } from "vue";

import Layout from "@/Layouts/Master.vue";
import Header from "./Header.vue";

import Link from "./Link/Index.vue";
import Click from "./Click/Index.vue";
import Source from "./Source/Index.vue";
import Device from "./Device/Index.vue";
import Location from "./Location/Index.vue";

const range = ref(null);

const setRange = (data) => {
    range.value = data;
};
</script>

<template>
    <Head title="Analytics" />

    <Layout>
        <div class="p-6">
            <Header @update="setRange" />

            <div class="space-y-4" v-if="range">
                <div class="grid grid-cols-12 gap-4">
                    <Click :dateRange="range" />
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <Link :dateRange="range" />
                    <Source :dateRange="range" />
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <Location :dateRange="range" />
                    <Device :dateRange="range" />
                </div>
            </div>
        </div>
    </Layout>
</template>
