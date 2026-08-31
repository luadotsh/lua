<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

/**
 * Contents list for an article: click a heading to glide to it, and the
 * heading you are reading stays marked as you scroll.
 *
 * The ids come from the server, which is the same pass that wrote them into
 * the HTML — so the list can never point at an anchor the article does not
 * have. Nothing here reads the DOM to build itself; it only observes it.
 */
const props = defineProps<{
    headings: Array<{ id: string; text: string; level: number }>;
}>();

// Matches the `scroll-mt` on the headings: the observer's trigger line has to
// sit where a heading comes to rest, or the marker is a section behind.
const HEADER_OFFSET = 96;

const activeId = ref<string>(props.headings[0]?.id ?? '');

let observer: IntersectionObserver | null = null;

// While a smooth scroll is in flight the observer fires for every heading it
// passes, which makes the marker run down the list. Pin it to the target until
// the scroll settles.
let pinnedUntil = 0;

onMounted(() => {
    if (props.headings.length === 0) {
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (Date.now() < pinnedUntil) {
                return;
            }

            const topmost = entries
                .filter((entry) => entry.isIntersecting)
                .map((entry) => entry.target as HTMLElement)
                .sort(
                    (a, b) =>
                        a.getBoundingClientRect().top - b.getBoundingClientRect().top,
                )[0];

            if (topmost) {
                activeId.value = topmost.id;
            }
        },
        // Trigger below the header, and ignore the bottom 70% of the screen so
        // the section being read stays marked rather than the one arriving.
        { rootMargin: `-${HEADER_OFFSET}px 0px -70% 0px`, threshold: 0 },
    );

    for (const heading of props.headings) {
        const element = document.getElementById(heading.id);

        if (element) {
            observer.observe(element);
        }
    }
});

onBeforeUnmount(() => {
    observer?.disconnect();
    observer = null;
});

/**
 * The anchor is left to do the navigating: the browser scrolls smoothly on its
 * own (`scroll-behavior` in app.css) and lands correctly under the sticky
 * header (`scroll-mt` on the headings).
 *
 * Scripting the scroll instead was tried and is worse here — Inertia rewrites
 * `history` every time it saves a scroll position, so a hash set from
 * JavaScript is wiped moments later and the section becomes unlinkable.
 *
 * All this does is mark the target immediately, so the highlight does not
 * chase the scroll down the list on its way there.
 */
const markActive = (id: string): void => {
    activeId.value = id;
    pinnedUntil = Date.now() + 700;
};
</script>

<template>
    <nav v-if="headings.length > 0" aria-label="On this page" data-testid="blog-toc">
        <h2 class="text-xs font-medium tracking-wide text-muted-foreground uppercase">
            On this page
        </h2>
        <ul class="mt-4 space-y-0.5 border-l border-border">
            <li v-for="heading in headings" :key="heading.id">
                <a
                    :href="`#${heading.id}`"
                    :data-testid="`toc-${heading.id}`"
                    :aria-current="activeId === heading.id ? 'location' : undefined"
                    class="-ml-px block border-l py-1.5 leading-snug transition-colors"
                    :class="[
                        heading.level === 3 ? 'pl-7 text-xs' : 'pl-4 text-sm',
                        activeId === heading.id
                            ? 'border-foreground font-medium text-foreground'
                            : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground',
                    ]"
                    @click="markActive(heading.id)"
                >
                    {{ heading.text }}
                </a>
            </li>
        </ul>
    </nav>
</template>
