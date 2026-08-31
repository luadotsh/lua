<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { IconBrandGithub, IconBrandX, IconMenu2, IconX } from '@tabler/icons-vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { login, register } from '@/routes';
import { index as linksIndex } from '@/routes/links';
import site from '@/routes/site';

/**
 * The public shell: a header that scrolls with the page and a real footer.
 * Nothing from the app layout — no sidebar, no workspace, no scroll
 * container — because a marketing page is read top to bottom.
 */
const page = usePage();

// The site is served to signed-in people too, so the header offers the app
// rather than a second invitation to sign up.
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const open = ref(false);

const socials = [
    { label: 'Lua on GitHub', href: 'https://github.com/luadotsh/lua', icon: IconBrandGithub },
    { label: 'Lua on X', href: 'https://x.com/luadotsh', icon: IconBrandX },
];

type FooterLink = { label: string; href: string; external?: boolean };

// Four columns, because there is now enough of a site to need them. Grouped by
// what a reader is looking for rather than by how the routes are organised.
const footerColumns: Array<{ title: string; links: FooterLink[] }> = [
    {
        title: 'Product',
        links: [
            { label: 'Pricing', href: site.pricing.url() },
            { label: 'Use cases', href: site.useCases.index.url() },
            { label: 'Tools', href: site.tools.index.url() },
            { label: 'Alternatives', href: site.alternatives.index.url() },
        ],
    },
    {
        title: 'Tools',
        links: [
            { label: 'UTM builder', href: site.tools.utmBuilder.url() },
            { label: 'QR generator', href: site.tools.qrGenerator.url() },
            { label: 'Redirect checker', href: site.tools.linkChecker.url() },
        ],
    },
    {
        title: 'Learn',
        links: [
            { label: 'Blog', href: site.blog.index.url() },
            { label: 'Glossary', href: site.glossary.index.url() },
            { label: 'FAQ', href: site.faq.url() },
        ],
    },
    {
        title: 'Company',
        links: [
            { label: 'Terms', href: site.terms.url() },
            { label: 'Privacy', href: site.privacy.url() },
            { label: 'Source', href: 'https://github.com/luadotsh/lua', external: true },
        ],
    },
];

const nav = [
    { key: 'pricing', label: 'Pricing', href: site.pricing.url() },
    { key: 'use-cases', label: 'Use cases', href: site.useCases.index.url() },
    { key: 'tools', label: 'Tools', href: site.tools.index.url() },
    { key: 'blog', label: 'Blog', href: site.blog.index.url() },
    { key: 'alternatives', label: 'Alternatives', href: site.alternatives.index.url() },
];
</script>

<template>
    <div class="site-light flex min-h-screen flex-col">
        <header class="sticky top-0 z-40 border-b border-border bg-background/90 backdrop-blur">
            <div class="mx-auto flex h-16 w-full max-w-6xl items-center gap-6 px-4 sm:px-6">
                <Link :href="site.home.url()" class="flex shrink-0 items-center gap-2">
                    <img src="/images/lua/full-black.svg" alt="Lua" class="h-7 w-auto" />
                </Link>

                <nav class="hidden items-center gap-6 md:flex">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        :data-testid="`site-nav-${item.key}`"
                        class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="ml-auto hidden items-center gap-2 md:flex">
                    <Button v-if="isAuthenticated" as-child>
                        <Link :href="linksIndex.url()">Go to app</Link>
                    </Button>
                    <template v-else>
                        <Button variant="ghost" as-child>
                            <Link :href="login.url()">Sign in</Link>
                        </Button>
                        <Button as-child>
                            <Link :href="register.url()">Start for free</Link>
                        </Button>
                    </template>
                </div>

                <button
                    type="button"
                    class="ml-auto md:hidden"
                    :aria-label="open ? 'Close menu' : 'Open menu'"
                    @click="open = !open"
                >
                    <IconX v-if="open" class="size-5" />
                    <IconMenu2 v-else class="size-5" />
                </button>
            </div>

            <div v-if="open" class="border-t border-border md:hidden">
                <nav class="mx-auto flex w-full max-w-6xl flex-col gap-1 px-4 py-3">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-md px-2 py-2 text-sm text-muted-foreground hover:bg-accent hover:text-foreground"
                        @click="open = false"
                    >
                        {{ item.label }}
                    </Link>
                    <Link
                        :href="isAuthenticated ? linksIndex.url() : register.url()"
                        class="rounded-md px-2 py-2 text-sm font-medium hover:bg-accent"
                    >
                        {{ isAuthenticated ? 'Go to app' : 'Start for free' }}
                    </Link>
                </nav>
            </div>
        </header>

        <!--
            The rails live here, not in the pages: one ruled column running the
            whole height, which every section divider then crosses. Pages
            supply padding and their own bottom rule and nothing else.
        -->
        <main class="grow">
            <div class="site-rail relative mx-auto w-full max-w-6xl">
                <slot />
            </div>
        </main>

        <footer class="brand-panel">
            <div class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
                <div class="grid gap-12 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,2.6fr)]">
                    <!-- Brand block: mark, one line, and the two places to go. -->
                    <div>
                        <Link :href="site.home.url()" class="inline-block">
                            <img src="/images/lua/full-white.svg" alt="Lua" class="h-7 w-auto" />
                        </Link>
                        <p class="mt-4 max-w-xs text-sm leading-relaxed text-muted-foreground">
                            Short links, and the story of every click. Open
                            source, and yours to self-host.
                        </p>

                        <div class="mt-6 flex items-center gap-2">
                            <a
                                v-for="social in socials"
                                :key="social.label"
                                :href="social.href"
                                :aria-label="social.label"
                                rel="noopener noreferrer"
                                target="_blank"
                                class="inline-flex size-9 items-center justify-center rounded-full border border-border text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground"
                            >
                                <component :is="social.icon" class="size-4" />
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8 sm:grid-cols-4">
                        <div v-for="column in footerColumns" :key="column.title">
                            <h2 class="text-xs font-medium tracking-wide text-muted-foreground uppercase">
                                {{ column.title }}
                            </h2>
                            <ul class="mt-4 space-y-2.5 text-sm">
                                <li v-for="item in column.links" :key="item.label">
                                    <a
                                        v-if="item.external"
                                        :href="item.href"
                                        rel="noopener noreferrer"
                                        class="text-muted-foreground transition-colors hover:text-foreground"
                                    >
                                        {{ item.label }}
                                    </a>
                                    <Link
                                        v-else
                                        :href="item.href"
                                        class="text-muted-foreground transition-colors hover:text-foreground"
                                    >
                                        {{ item.label }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
