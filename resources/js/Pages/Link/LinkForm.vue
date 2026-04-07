<script setup lang="ts">
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/components/ui/collapsible";
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxViewport,
    ComboboxTrigger,
} from "@/components/ui/combobox";
import { DateTimePicker } from "@/components/ui/date-time-picker";
import { IconChevronDown, IconCheck } from "@tabler/icons-vue";
import { Button } from "@/components/ui/button";

interface Tag {
    id: string | number;
    name: string;
}

interface FormData {
    url: string;
    domain: string;
    key: string;
    tags: (string | number)[];
    ios: string;
    android: string;
    expires_at: string;
    expired_redirect_url: string;
    password: string;
    errors: Record<string, string>;
    [key: string]: any;
}

const props = defineProps<{
    form: FormData;
    expiresAtDate: string;
}>();

const emit = defineEmits<{
    'update:expiresAtDate': [value: string];
}>();

const domains = usePage().props.domains as string[];
const allTags = usePage().props.tags as Tag[];

const generalOpen = ref(true);
const trafficOpen = ref(false);
const passwordOpen = ref(false);
const expirationOpen = ref(false);

// Domain combobox
const domainSearch = ref("");
const filteredDomains = computed(() =>
    domains.filter((d) => d.toLowerCase().includes(domainSearch.value.toLowerCase()))
);

// Tags combobox — reka-ui Combobox works with object values, so we track by id
const selectedTags = computed({
    get: () => allTags.filter((t) => props.form.tags.includes(t.id)),
    set: (tags: Tag[]) => {
        props.form.tags = tags.map((t) => t.id);
    },
});

const tagSearch = ref("");
const filteredTags = computed(() =>
    allTags.filter((t) => t.name.toLowerCase().includes(tagSearch.value.toLowerCase()))
);

const selectedTagsLabel = computed(() => {
    if (selectedTags.value.length === 0) return "Select tags...";
    if (selectedTags.value.length === 1) return selectedTags.value[0].name;
    return `${selectedTags.value.length} tags selected`;
});
</script>

<template>
    <div class="flex flex-col gap-3">
        <!-- General -->
        <Collapsible v-model:open="generalOpen">
            <CollapsibleTrigger
                class="w-full rounded-lg bg-muted hover:bg-muted/80 cursor-pointer text-sm px-4 py-2.5 flex items-center justify-between"
            >
                <span class="font-medium text-foreground">General</span>
                <IconChevronDown
                    class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                    :class="{ 'rotate-180': generalOpen }"
                />
            </CollapsibleTrigger>
            <CollapsibleContent>
                <div class="grid grid-cols-6 gap-x-4 gap-y-4 px-1 pt-4 pb-2">

                    <!-- Domain combobox -->
                    <div class="col-span-6 lg:col-span-3 grid gap-2">
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
                                        <IconChevronDown class="h-4 w-4 opacity-50 shrink-0" />
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
                                    <ComboboxItem
                                        v-for="d in filteredDomains"
                                        :key="d"
                                        :value="d"
                                    >
                                        {{ d }}
                                        <ComboboxItemIndicator class="ml-auto">
                                            <IconCheck class="h-4 w-4" />
                                        </ComboboxItemIndicator>
                                    </ComboboxItem>
                                </ComboboxViewport>
                            </ComboboxList>
                        </Combobox>
                        <p v-if="form.errors.domain" class="text-sm text-destructive">{{ form.errors.domain }}</p>
                    </div>

                    <!-- Custom back-half -->
                    <div class="col-span-6 lg:col-span-3 grid gap-2">
                        <Label for="key">Custom back-half (optional)</Label>
                        <Input id="key" type="text" v-model="form.key" placeholder="e.g. super-link" />
                        <p v-if="form.errors.key" class="text-sm text-destructive">{{ form.errors.key }}</p>
                    </div>

                    <!-- Destination URL -->
                    <div class="col-span-6 grid gap-2">
                        <Label for="url">Destination URL <span class="text-destructive">*</span></Label>
                        <Input id="url" type="text" v-model="form.url" placeholder="e.g. https://example.com" />
                        <p v-if="form.errors.url" class="text-sm text-destructive">{{ form.errors.url }}</p>
                    </div>

                    <!-- Tags multi-select combobox -->
                    <div class="col-span-6 grid gap-2">
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
                                        <span class="text-muted-foreground truncate">{{ selectedTagsLabel }}</span>
                                        <IconChevronDown class="h-4 w-4 opacity-50 shrink-0" />
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
                                    <ComboboxItem
                                        v-for="tag in filteredTags"
                                        :key="tag.id"
                                        :value="tag"
                                    >
                                        {{ tag.name }}
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
            </CollapsibleContent>
        </Collapsible>

        <!-- Targeted Traffic -->
        <Collapsible v-model:open="trafficOpen">
            <CollapsibleTrigger
                class="w-full rounded-lg bg-muted hover:bg-muted/80 cursor-pointer text-sm px-4 py-2.5 flex items-center justify-between"
            >
                <span class="font-medium text-foreground">Targeted Traffic</span>
                <IconChevronDown
                    class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                    :class="{ 'rotate-180': trafficOpen }"
                />
            </CollapsibleTrigger>
            <CollapsibleContent>
                <div class="grid grid-cols-6 gap-x-4 gap-y-4 px-1 pt-4 pb-2">
                    <div class="col-span-6 grid gap-2">
                        <Label for="ios">iOS URL</Label>
                        <Input id="ios" type="text" v-model="form.ios" placeholder="e.g. https://apps.apple.com/app/333903271" />
                        <p v-if="form.errors.ios" class="text-sm text-destructive">{{ form.errors.ios }}</p>
                    </div>
                    <div class="col-span-6 grid gap-2">
                        <Label for="android">Android URL</Label>
                        <Input id="android" type="text" v-model="form.android" placeholder="e.g. https://play.google.com/store/apps/details?id=com.twitter.android" />
                        <p v-if="form.errors.android" class="text-sm text-destructive">{{ form.errors.android }}</p>
                    </div>
                </div>
            </CollapsibleContent>
        </Collapsible>

        <!-- Link Password -->
        <Collapsible v-model:open="passwordOpen">
            <CollapsibleTrigger
                class="w-full rounded-lg bg-muted hover:bg-muted/80 cursor-pointer text-sm px-4 py-2.5 flex items-center justify-between"
            >
                <span class="font-medium text-foreground">Link Password</span>
                <IconChevronDown
                    class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                    :class="{ 'rotate-180': passwordOpen }"
                />
            </CollapsibleTrigger>
            <CollapsibleContent>
                <div class="grid grid-cols-6 gap-x-4 gap-y-4 px-1 pt-4 pb-2">
                    <div class="col-span-6 grid gap-2">
                        <Label for="password">Password</Label>
                        <Input id="password" type="text" v-model="form.password" placeholder="Create a password" />
                        <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
                    </div>
                </div>
            </CollapsibleContent>
        </Collapsible>

        <!-- Expiration -->
        <Collapsible v-model:open="expirationOpen">
            <CollapsibleTrigger
                class="w-full rounded-lg bg-muted hover:bg-muted/80 cursor-pointer text-sm px-4 py-2.5 flex items-center justify-between"
            >
                <span class="font-medium text-foreground">Expiration</span>
                <IconChevronDown
                    class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                    :class="{ 'rotate-180': expirationOpen }"
                />
            </CollapsibleTrigger>
            <CollapsibleContent>
                <div class="grid grid-cols-6 gap-x-4 gap-y-4 px-1 pt-4 pb-2">
                    <div class="col-span-6 grid gap-2">
                        <Label>Date and Time</Label>
                        <DateTimePicker
                            :model-value="expiresAtDate"
                            @update:model-value="emit('update:expiresAtDate', $event)"
                        />
                        <p v-if="form.errors.expires_at" class="text-sm text-destructive">{{ form.errors.expires_at }}</p>
                    </div>
                    <div class="col-span-6 grid gap-2">
                        <Label for="expired_redirect_url">Redirect users to a specific URL</Label>
                        <Input id="expired_redirect_url" type="text" v-model="form.expired_redirect_url" placeholder="e.g. https://example.com" />
                        <p v-if="form.errors.expired_redirect_url" class="text-sm text-destructive">{{ form.errors.expired_redirect_url }}</p>
                    </div>
                </div>
            </CollapsibleContent>
        </Collapsible>
    </div>
</template>
