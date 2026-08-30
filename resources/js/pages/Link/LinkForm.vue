<script setup lang="ts">
import {
    IconCalendarEvent,
    IconCheck,
    IconChevronDown,
    IconDeviceMobile,
    IconLock,
    IconTargetArrow,
} from "@tabler/icons-vue";
import { usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import BehaviourRow from "@/components/links/BehaviourRow.vue";
import { Button } from "@/components/ui/button";
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
    ComboboxViewport,
} from "@/components/ui/combobox";
import { DateTimePicker } from "@/components/ui/date-time-picker";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { buildDestination } from "@/lib/destination";

interface Tag {
    id: string | number;
    name: string;
    color?: string;
}

interface FormData {
    url: string;
    domain: string;
    key: string;
    tags: (string | number)[];
    ios?: string;
    android?: string;
    expires_at?: string;
    expired_redirect_url?: string;
    password?: string;
    utm_source?: string;
    utm_medium?: string;
    utm_campaign?: string;
    utm_term?: string;
    utm_content?: string;
    errors: Record<string, string>;
    [key: string]: any;
}

const props = defineProps<{
    form: FormData;
    expiresAtDate: string;
}>();

const emit = defineEmits<{
    "update:expiresAtDate": [value: string];
}>();

const domains = usePage().props.domains as string[];
const allTags = usePage().props.tags as Tag[];

const domainSearch = ref("");
const tagSearch = ref("");

const filteredDomains = computed(() =>
    domains.filter((d) => d.toLowerCase().includes(domainSearch.value.toLowerCase())),
);

const filteredTags = computed(() =>
    allTags.filter((t) => t.name.toLowerCase().includes(tagSearch.value.toLowerCase())),
);

const selectedTags = computed({
    get: () => allTags.filter((t) => props.form.tags.includes(t.id)),
    set: (tags: Tag[]) => {
        props.form.tags = tags.map((t) => t.id);
    },
});

const selectedTagsLabel = computed(() => {
    if (selectedTags.value.length === 0) return "Select tags...";
    if (selectedTags.value.length === 1) return selectedTags.value[0].name;
    return `${selectedTags.value.length} tags selected`;
});

// --- the destination preview ------------------------------------------------

const destination = computed(() =>
    buildDestination(props.form.url, {
        utm_source: props.form.utm_source,
        utm_medium: props.form.utm_medium,
        utm_campaign: props.form.utm_campaign,
        utm_term: props.form.utm_term,
        utm_content: props.form.utm_content,
    }),
);

// --- behaviours -------------------------------------------------------------

const UTM_FIELDS = [
    { name: "utm_source", label: "Source", short: "source", placeholder: "e.g. newsletter" },
    { name: "utm_medium", label: "Medium", short: "medium", placeholder: "e.g. email" },
    { name: "utm_campaign", label: "Campaign", short: "campaign", placeholder: "e.g. spring-launch" },
    { name: "utm_term", label: "Term", short: "term", placeholder: "e.g. url-shortener" },
    { name: "utm_content", label: "Content", short: "content", placeholder: "e.g. header-button" },
] as const;

const campaignSummary = computed(() =>
    UTM_FIELDS.filter((f) => props.form[f.name])
        .map((f) => `${f.short}: ${props.form[f.name]}`)
        .join(" · "),
);

const targetingSummary = computed(() => {
    const platforms = [props.form.ios && "iOS", props.form.android && "Android"].filter(Boolean);

    return platforms.length ? `${platforms.join(" and ")} visitors go elsewhere` : "";
});

const passwordSummary = computed(() =>
    props.form.password ? "Visitors must enter a password" : "",
);

const expirationSummary = computed(() => {
    if (!props.expiresAtDate) {
        return "";
    }

    const on = new Date(props.expiresAtDate).toLocaleDateString(undefined, {
        month: "short",
        day: "numeric",
        year: "numeric",
    });

    return props.form.expired_redirect_url
        ? `Expires ${on} · then redirects`
        : `Expires ${on}`;
});

// Open on load whatever is already configured, so an edit does not start with
// everything hidden.
const openRows = ref({
    campaign: false,
    targeting: false,
    password: false,
    expiration: false,
});
</script>

<template>
    <div class="flex flex-col gap-8">
        <!-- What the link is -->
        <div class="flex flex-col gap-4">
            <div class="grid gap-2">
                <Label for="url">Destination URL <span class="text-destructive">*</span></Label>
                <Input
                    id="url"
                    v-model="form.url"
                    type="text"
                    placeholder="e.g. https://example.com"
                />
                <p v-if="form.errors.url" class="text-sm text-destructive">{{ form.errors.url }}</p>
            </div>

            <!-- Without this the UTM fields are invisible until someone clicks
                 the link in the wild. -->
            <div
                v-if="destination"
                class="flex flex-col gap-1 rounded-lg border border-dashed border-input bg-background px-3 py-2.5"
            >
                <span class="text-[11px] font-medium tracking-wide text-muted-foreground uppercase">
                    Visitors will land on
                </span>
                <span class="font-mono text-xs break-all text-foreground/80">
                    {{ destination.base
                    }}<span class="text-violet-400">{{ destination.added }}</span>
                </span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Short Link <span class="text-destructive">*</span></Label>
                    <Combobox
                        :model-value="form.domain"
                        @update:model-value="(v) => (form.domain = v as string)"
                        @update:open="(o) => { if (o) domainSearch = '' }"
                    >
                        <ComboboxAnchor as-child>
                            <ComboboxTrigger as-child>
                                <Button variant="outline" class="w-full justify-between font-normal">
                                    <span :class="!form.domain ? 'text-muted-foreground' : ''">
                                        {{ form.domain || "Select domain..." }}
                                    </span>
                                    <IconChevronDown class="h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </ComboboxTrigger>
                        </ComboboxAnchor>
                        <ComboboxList align="start" class="w-[var(--reka-combobox-trigger-width)]">
                            <ComboboxInput
                                v-model="domainSearch"
                                :display-value="() => domainSearch"
                                placeholder="Search domain..."
                                auto-focus
                            />
                            <ComboboxViewport class="p-1">
                                <ComboboxEmpty class="py-3 text-center text-sm text-muted-foreground">
                                    No domains found.
                                </ComboboxEmpty>
                                <ComboboxItem v-for="d in filteredDomains" :key="d" :value="d">
                                    {{ d }}
                                    <ComboboxItemIndicator class="ml-auto">
                                        <IconCheck class="h-4 w-4" />
                                    </ComboboxItemIndicator>
                                </ComboboxItem>
                            </ComboboxViewport>
                        </ComboboxList>
                    </Combobox>
                    <p v-if="form.errors.domain" class="text-sm text-destructive">
                        {{ form.errors.domain }}
                    </p>
                </div>

                <div class="grid gap-2">
                    <Label for="key">
                        Custom back-half
                        <span class="font-normal text-muted-foreground">(optional)</span>
                    </Label>
                    <Input id="key" v-model="form.key" type="text" placeholder="e.g. super-link" />
                    <p v-if="form.errors.key" class="text-sm text-destructive">
                        {{ form.errors.key }}
                    </p>
                </div>
            </div>

            <!-- Tags organise the link; they are not part of its address. -->
            <div class="grid gap-2">
                <Label>Tags</Label>
                <Combobox
                    v-model="selectedTags"
                    multiple
                    by="id"
                    @update:open="(o) => { if (o) tagSearch = '' }"
                >
                    <ComboboxAnchor as-child>
                        <ComboboxTrigger as-child>
                            <Button variant="outline" class="w-full justify-between font-normal">
                                <span class="flex min-w-0 items-center gap-1.5">
                                    <template v-if="selectedTags.length">
                                        <span
                                            v-for="tag in selectedTags"
                                            :key="tag.id"
                                            class="flex items-center gap-1.5 truncate"
                                        >
                                            <span
                                                v-if="tag.color"
                                                class="size-2 shrink-0 rounded-full"
                                                :style="{ backgroundColor: tag.color }"
                                            />
                                            {{ tag.name }}
                                        </span>
                                    </template>
                                    <span v-else class="truncate text-muted-foreground">
                                        {{ selectedTagsLabel }}
                                    </span>
                                </span>
                                <IconChevronDown class="h-4 w-4 shrink-0 opacity-50" />
                            </Button>
                        </ComboboxTrigger>
                    </ComboboxAnchor>
                    <ComboboxList align="start" class="w-[var(--reka-combobox-trigger-width)]">
                        <ComboboxInput
                            v-model="tagSearch"
                            :display-value="() => tagSearch"
                            placeholder="Search tags..."
                            auto-focus
                        />
                        <ComboboxViewport class="p-1">
                            <ComboboxEmpty class="py-3 text-center text-sm text-muted-foreground">
                                No tags found.
                            </ComboboxEmpty>
                            <ComboboxItem v-for="tag in filteredTags" :key="tag.id" :value="tag">
                                <span class="flex items-center gap-2">
                                    <span
                                        v-if="tag.color"
                                        class="size-2 shrink-0 rounded-full"
                                        :style="{ backgroundColor: tag.color }"
                                    />
                                    {{ tag.name }}
                                </span>
                                <ComboboxItemIndicator class="ml-auto">
                                    <IconCheck class="h-4 w-4" />
                                </ComboboxItemIndicator>
                            </ComboboxItem>
                        </ComboboxViewport>
                    </ComboboxList>
                </Combobox>
                <p v-if="form.errors.tags" class="text-sm text-destructive">{{ form.errors.tags }}</p>
            </div>
        </div>

        <!-- What the link does -->
        <div class="flex flex-col">
            <div class="flex items-baseline justify-between gap-3 border-b border-border pb-2.5">
                <h2 class="text-sm font-semibold text-foreground">Behaviour</h2>
                <p class="text-xs text-muted-foreground">
                    What this link does beyond redirecting
                </p>
            </div>

            <div class="mt-3.5 flex flex-col gap-2">
                <BehaviourRow
                    v-model:open="openRows.campaign"
                    :icon="IconTargetArrow"
                    title="Campaign (UTM)"
                    description="Tag the destination so its analytics can attribute the visit"
                    :summary="campaignSummary"
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-for="field in UTM_FIELDS" :key="field.name" class="grid gap-2">
                            <Label :for="field.name">{{ field.label }}</Label>
                            <Input
                                :id="field.name"
                                v-model="form[field.name]"
                                type="text"
                                :placeholder="field.placeholder"
                            />
                            <p v-if="form.errors[field.name]" class="text-sm text-destructive">
                                {{ form.errors[field.name] }}
                            </p>
                        </div>
                    </div>
                </BehaviourRow>

                <BehaviourRow
                    v-model:open="openRows.targeting"
                    :icon="IconDeviceMobile"
                    title="Platform targeting"
                    description="Send iOS or Android visitors somewhere else"
                    :summary="targetingSummary"
                >
                    <div class="grid gap-2">
                        <Label for="ios">iOS URL</Label>
                        <Input
                            id="ios"
                            v-model="form.ios"
                            type="text"
                            placeholder="e.g. https://apps.apple.com/app/333903271"
                        />
                        <p v-if="form.errors.ios" class="text-sm text-destructive">
                            {{ form.errors.ios }}
                        </p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="android">Android URL</Label>
                        <Input
                            id="android"
                            v-model="form.android"
                            type="text"
                            placeholder="e.g. https://play.google.com/store/apps/details?id=com.twitter.android"
                        />
                        <p v-if="form.errors.android" class="text-sm text-destructive">
                            {{ form.errors.android }}
                        </p>
                    </div>
                </BehaviourRow>

                <BehaviourRow
                    v-model:open="openRows.password"
                    :icon="IconLock"
                    title="Password"
                    description="Ask for a password before redirecting"
                    :summary="passwordSummary"
                >
                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="text"
                            placeholder="Create a password"
                        />
                        <p v-if="form.errors.password" class="text-sm text-destructive">
                            {{ form.errors.password }}
                        </p>
                    </div>
                </BehaviourRow>

                <BehaviourRow
                    v-model:open="openRows.expiration"
                    :icon="IconCalendarEvent"
                    title="Expiration"
                    description="Stop the link working after a date"
                    :summary="expirationSummary"
                >
                    <div class="grid gap-2">
                        <Label>Date and time</Label>
                        <DateTimePicker
                            :model-value="expiresAtDate"
                            @update:model-value="emit('update:expiresAtDate', $event)"
                        />
                        <p v-if="form.errors.expires_at" class="text-sm text-destructive">
                            {{ form.errors.expires_at }}
                        </p>
                    </div>

                    <!-- The server requires a date whenever this is filled, so
                         the field waits for one instead of letting the rule be
                         discovered through an error. -->
                    <div class="grid gap-2" :class="{ 'opacity-50': !expiresAtDate }">
                        <Label for="expired_redirect_url">Then redirect to</Label>
                        <Input
                            id="expired_redirect_url"
                            v-model="form.expired_redirect_url"
                            type="text"
                            :disabled="!expiresAtDate"
                            placeholder="e.g. https://example.com"
                        />
                        <p v-if="!expiresAtDate" class="text-xs text-muted-foreground">
                            Pick a date first.
                        </p>
                        <p v-if="form.errors.expired_redirect_url" class="text-sm text-destructive">
                            {{ form.errors.expired_redirect_url }}
                        </p>
                    </div>
                </BehaviourRow>
            </div>
        </div>
    </div>
</template>
