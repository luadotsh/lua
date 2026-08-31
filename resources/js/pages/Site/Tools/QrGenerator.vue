<script setup lang="ts">
import { IconDownload } from '@tabler/icons-vue';
import { ref, watch } from 'vue';

import JsonLd from '@/components/site/JsonLd.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import Seo from '@/components/site/Seo.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SiteLayout from '@/layouts/site/SiteLayout.vue';

defineProps<{ seo: { title: string; description: string } }>();

const value = ref('https://example.com/spring');
const png = ref<string>('');
const svg = ref<string>('');
const error = ref<string>('');

/**
 * `qrcode` is imported dynamically and only in the browser: it is a chunk this
 * page needs and no other, and the SSR pass has no canvas to draw on.
 */
const render = async (): Promise<void> => {
    const text = value.value.trim();

    if (text === '') {
        png.value = '';
        svg.value = '';
        error.value = '';

        return;
    }

    try {
        const QRCode = (await import('qrcode')).default;

        // Error correction M leaves the code readable with a quarter of it
        // damaged, which is the right trade for something that gets printed.
        const options = {
            errorCorrectionLevel: 'M' as const,
            margin: 2,
            width: 512,
        };

        png.value = await QRCode.toDataURL(text, options);
        svg.value = await QRCode.toString(text, { ...options, type: 'svg' });
        error.value = '';
    } catch {
        png.value = '';
        svg.value = '';
        error.value = 'That is too long to encode as a QR code.';
    }
};

watch(value, render, { immediate: true });

const download = (format: 'png' | 'svg'): void => {
    const href =
        format === 'png'
            ? png.value
            : `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg.value)}`;

    if (href === '') {
        return;
    }

    const anchor = document.createElement('a');
    anchor.href = href;
    anchor.download = `qr-code.${format}`;
    anchor.click();
};
</script>

<template>
    <Seo :title="seo.title" :description="seo.description" />
    <JsonLd
        :data="{
            '@type': 'WebApplication',
            name: 'QR code generator',
            applicationCategory: 'UtilitiesApplication',
            offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
        }"
    />

    <SiteLayout>
        <section class="mx-auto max-w-4xl px-6 py-16 sm:px-10 sm:py-24">
            <PageHeader
                eyebrow="Tool"
                title="QR code generator"
                lead="Any URL, as a code you can print. Generated in your browser, so the address is never sent to us."
            />

            <div
                class="mt-12 grid gap-10 sm:grid-cols-[minmax(0,1fr)_16rem] sm:items-start"
            >
                <div>
                    <Label for="qr-value">URL or text</Label>
                    <Input
                        id="qr-value"
                        v-model="value"
                        data-testid="qr-value"
                        class="mt-2 font-mono"
                        placeholder="https://example.com/page"
                    />
                    <p v-if="error" class="mt-2 text-sm text-destructive">
                        {{ error }}
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <Button
                            variant="outline"
                            data-testid="qr-png"
                            :disabled="png === ''"
                            @click="download('png')"
                        >
                            <IconDownload class="size-4" />
                            PNG
                        </Button>
                        <Button
                            variant="outline"
                            data-testid="qr-svg"
                            :disabled="svg === ''"
                            @click="download('svg')"
                        >
                            <IconDownload class="size-4" />
                            SVG
                        </Button>
                    </div>

                    <p
                        class="mt-6 text-sm leading-relaxed text-muted-foreground"
                    >
                        Take the SVG for anything printed. It stays sharp at any
                        size, which a PNG scaled up to a poster does not.
                    </p>
                </div>

                <!--
                    White ground and a fixed dark foreground on purpose: a
                    scanner needs the contrast, and a code that followed the
                    page theme would stop working in dark mode.
                -->
                <div class="rounded-xl border border-border bg-white p-4">
                    <img
                        v-if="png"
                        :src="png"
                        alt="QR code for the text entered above"
                        data-testid="qr-image"
                        class="w-full"
                    />
                    <div
                        v-else
                        class="flex aspect-square items-center justify-center text-sm text-neutral-400"
                    >
                        Enter a URL
                    </div>
                </div>
            </div>

            <div class="mt-16 border-t border-border pt-10">
                <h2 class="font-display text-2xl font-semibold tracking-tight">
                    A code you print is a code you cannot edit
                </h2>
                <p class="mt-4 leading-relaxed text-muted-foreground">
                    This tool encodes whatever you give it, permanently. Point a
                    code at a short link on a domain you control and you can
                    change the destination later, count the scans separately
                    from clicks, and keep it working if you ever change
                    providers. Encode the destination directly and the poster is
                    the last decision you get to make.
                </p>
            </div>
        </section>
    </SiteLayout>
</template>
