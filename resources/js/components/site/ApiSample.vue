<script setup lang="ts">
import { IconCheck, IconCopy } from '@tabler/icons-vue';
import { computed, ref } from 'vue';

/**
 * The API section, with real endpoints.
 *
 * Every sample below calls a route that exists and returns the shape the
 * resource actually returns. A marketing page showing an endpoint the product
 * does not have is the fastest way to lose the one audience that checks.
 */
const samples = [
    {
        key: 'curl',
        label: 'cURL',
        code: `curl -X POST https://lua.sh/api/links \\
  -H "Authorization: Bearer $LUA_TOKEN" \\
  -H "Content-Type: application/json" \\
  -d '{
    "domain": "go.example.com",
    "key": "spring",
    "url": "https://example.com/collections/spring-2026",
    "utm_source": "newsletter",
    "utm_medium": "email"
  }'`,
    },
    {
        key: 'js',
        label: 'JavaScript',
        code: `const response = await fetch('https://lua.sh/api/links', {
    method: 'POST',
    headers: {
        Authorization: \`Bearer \${process.env.LUA_TOKEN}\`,
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        domain: 'go.example.com',
        key: 'spring',
        url: 'https://example.com/collections/spring-2026',
    }),
});

const link = await response.json();
// { id, domain, key, url, link, qr_code, tags, ... }`,
    },
    {
        key: 'php',
        label: 'PHP',
        code: `use Illuminate\\Support\\Facades\\Http;

$link = Http::withToken(config('services.lua.token'))
    ->post('https://lua.sh/api/links', [
        'domain' => 'go.example.com',
        'key' => 'spring',
        'url' => 'https://example.com/collections/spring-2026',
    ])
    ->json();`,
    },
    {
        key: 'mcp',
        label: 'MCP',
        code: `# Point an assistant at the MCP server and ask in words.

"Create a link on go.example.com pointing at the spring
 collection, tag it launch, and tell me how many clicks
 last week's newsletter link got from Brazil."

# The assistant creates the link, tags it, and reads the
# analytics back. No scraping layer in between.`,
    },
];

const active = ref(samples[0]!.key);
const current = computed(() => samples.find((sample) => sample.key === active.value)!);

const copied = ref(false);

const copy = async (): Promise<void> => {
    await navigator.clipboard.writeText(current.value.code);
    copied.value = true;
    window.setTimeout(() => (copied.value = false), 1600);
};
</script>

<template>
    <div class="site-card overflow-hidden" data-testid="api-sample">
        <div class="flex items-center gap-1 border-b border-border p-2">
            <button
                v-for="sample in samples"
                :key="sample.key"
                type="button"
                :data-testid="`api-tab-${sample.key}`"
                class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                :class="
                    active === sample.key
                        ? 'bg-accent text-accent-foreground'
                        : 'text-muted-foreground hover:text-foreground'
                "
                :aria-pressed="active === sample.key"
                @click="active = sample.key"
            >
                {{ sample.label }}
            </button>

            <button
                type="button"
                data-testid="api-copy"
                class="ml-auto inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
                @click="copy"
            >
                <IconCheck v-if="copied" class="size-4" />
                <IconCopy v-else class="size-4" />
                {{ copied ? 'Copied' : 'Copy' }}
            </button>
        </div>

        <!-- Its own scroll container: a long line must not widen the page. -->
        <div class="overflow-x-auto">
            <pre
                data-testid="api-code"
                class="p-6 font-mono text-[13px] leading-relaxed"
            ><code>{{ current.code }}</code></pre>
        </div>
    </div>
</template>
