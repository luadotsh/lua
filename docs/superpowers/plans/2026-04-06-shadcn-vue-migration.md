# Shadcn-Vue Frontend Migration Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Migrate Lua's entire frontend from HeadlessUI/JS to shadcn-vue (reka-ui)/TypeScript, replicating SendKit's UI architecture.

**Architecture:** Copy SendKit's shadcn ui/ components verbatim, rewrite all 99 Vue files to TypeScript, replace HeadlessUI with reka-ui primitives, migrate Inertia v2→v3 with Wayfinder routes, and adopt SendKit's layout/sidebar/toast/theme system.

**Tech Stack:** Vue 3, TypeScript, Inertia v3, reka-ui (shadcn-vue new-york-v4), Tailwind CSS v4, Wayfinder, @tabler/icons-vue, vue-sonner, @laravel/echo-vue

**Reference project:** `~/Code/sendkit` — copy components/layouts/composables from there.

**Spec:** `docs/superpowers/specs/2026-04-06-shadcn-vue-migration-design.md`

---

## Phase 1: Infrastructure (config, deps, TypeScript, CSS)

### Task 1: Install npm dependencies

**Files:**
- Modify: `package.json`

- [ ] **Step 1: Install new production dependencies**

```bash
npm install @inertiajs/vue3@^3.0.0 @tabler/icons-vue@^3.37.1 @vueuse/core@^12.8.2 class-variance-authority@^0.7.1 clsx@^2.1.1 reka-ui@^2.9.0 tailwind-merge@^3.2.0 tw-animate-css@^1.2.5 vue-sonner@^2.0.9
```

- [ ] **Step 2: Install new dev dependencies**

```bash
npm install -D @laravel/echo-vue@^2.3.0 @laravel/vite-plugin-wayfinder@^0.1.3 @types/node@^22.13.5 typescript@^5.2.2 vue-tsc@^2.2.4 laravel-vite-plugin@^2.0.0 vite@^7.0.4 @vitejs/plugin-vue@^6.0.0
```

- [ ] **Step 3: Remove old dependencies**

```bash
npm uninstall @headlessui/vue @phosphor-icons/vue floating-vue pusher-js
```

Note: keep `axios`, `laravel-echo`, `v-calendar`, `vue3-colorpicker`, `vuedraggable`, `chart.js`, `dayjs`, `qrcode`, `laravel-vue-i18n` — these stay.

- [ ] **Step 4: Commit**

```bash
git add package.json package-lock.json
git commit -m "chore: install shadcn-vue dependencies and remove old UI libs"
```

---

### Task 2: Install composer dependencies and publish Inertia v3 config

**Files:**
- Modify: `composer.json`
- Create: `config/inertia.php`

- [ ] **Step 1: Update composer deps**

```bash
composer require inertiajs/inertia-laravel:^3.0 laravel/wayfinder
composer remove tightenco/ziggy
```

- [ ] **Step 2: Publish Inertia v3 config**

```bash
php artisan vendor:publish --provider="Inertia\ServiceProvider" --force
php artisan view:clear
```

- [ ] **Step 3: Edit `config/inertia.php`**

Set `pages.paths` to `resource_path('js/pages')` (lowercase), disable SSR:

```php
'ssr' => [
    'enabled' => (bool) env('INERTIA_SSR_ENABLED', false),
],
'pages' => [
    'ensure_pages_exist' => false,
    'paths' => [resource_path('js/pages')],
    'extensions' => ['js', 'jsx', 'ts', 'tsx', 'vue'],
],
'testing' => ['ensure_pages_exist' => true],
'expose_shared_prop_keys' => true,
'history' => ['encrypt' => false],
```

- [ ] **Step 4: Commit**

```bash
git add composer.json composer.lock config/inertia.php
git commit -m "chore: upgrade Inertia to v3, add Wayfinder, remove Ziggy"
```

---

### Task 3: Add TypeScript config and shadcn config

**Files:**
- Create: `tsconfig.json`
- Create: `components.json`

- [ ] **Step 1: Copy `tsconfig.json` from SendKit**

```bash
cp ~/Code/sendkit/tsconfig.json ./tsconfig.json
```

No changes needed — paths already point to `resources/js/*`.

- [ ] **Step 2: Create `components.json`**

```bash
cp ~/Code/sendkit/components.json ./components.json
```

- [ ] **Step 3: Commit**

```bash
git add tsconfig.json components.json
git commit -m "chore: add TypeScript and shadcn-vue config"
```

---

### Task 4: Update Vite config

**Files:**
- Delete: `vite.config.js`
- Create: `vite.config.ts`

- [ ] **Step 1: Create `vite.config.ts`**

```ts
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

- [ ] **Step 2: Delete old config**

```bash
rm vite.config.js
```

- [ ] **Step 3: Commit**

```bash
git add vite.config.ts
git commit -m "chore: migrate vite config to TypeScript with Wayfinder"
```

---

### Task 5: Replace `app.css` with SendKit's design token system

**Files:**
- Rewrite: `resources/css/app.css`

- [ ] **Step 1: Copy SendKit's `app.css`**

```bash
cp ~/Code/sendkit/resources/css/app.css resources/css/app.css
```

- [ ] **Step 2: Adapt for Lua**

Edit `resources/css/app.css`:
- Change font from `'Space Grotesk'` to `'Inter'` in both `:root` and `.dark` sections (2 places each)
- Keep the workflow-dots and workflow-line styles (they're specific to SendKit but harmless)
- Add back Lua-specific styles at the bottom:

```css
/* Lua-specific */
.prose-formatter {
    @apply prose prose-sm dark:prose-invert prose-img:rounded-lg max-w-full text-zinc-800 dark:text-zinc-200 p-4 focus:outline-none overflow-auto min-w-full;
}

p:empty:not(:last-child)::after {
    content: "\00A0";
}

.vc-input-toggle {
    display: none !important;
}
.vc-color-wrap {
    border-radius: 4px !important;
    width: 32px !important;
}
```

- [ ] **Step 3: Delete old CSS files that are no longer needed**

```bash
rm resources/css/v-calendar.css resources/css/floating.css
```

- [ ] **Step 4: Commit**

```bash
git add resources/css/app.css
git commit -m "chore: replace CSS with SendKit design token system"
```

---

### Task 6: Update `app.blade.php`

**Files:**
- Modify: `resources/views/app.blade.php`

- [ ] **Step 1: Rewrite `app.blade.php`**

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        <title data-inertia>{{ config('app.name', 'Lua') }}</title>

        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..700&display=swap" rel="stylesheet">

        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
```

Key changes from current:
- `@routes` removed (Ziggy gone)
- `<title inertia>` → `<title data-inertia>` (Inertia v3)
- `app.js` → `app.ts`
- Dark mode detection script added
- `$appearance` class binding on `<html>`
- Removed `class="h-full"` from html/body

- [ ] **Step 2: Commit**

```bash
git add resources/views/app.blade.php
git commit -m "chore: update blade template for Inertia v3 and dark mode"
```

---

### Task 7: Update `HandleInertiaRequests.php`

**Files:**
- Modify: `app/Http/Middleware/HandleInertiaRequests.php`

- [ ] **Step 1: Add `sidebarOpen` and `appearance` shared props**

```php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'name' => config('app.name'),
        'auth' => [
            'user' => function () use ($request) {
                if (! $request->user()) {
                    return;
                }

                $currentWorkspace = $request->user()->current_workspace_id ? $request->user()->currentWorkspace : null;
                $currentWorkspace ? $currentWorkspace->role = $request->user()->workspaceRole($currentWorkspace) : null;

                return array_merge($request->user()->toArray(), array_filter([
                    'current_workspace' => $currentWorkspace,
                    'workspaces' => $request->user()->workspaces,
                ]));
            },
        ],
        'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        'flash' => $request->session()->get('flash', []),
        'env' => config('app.env'),
        'locale' => app()->getLocale(),
    ];
}
```

Also add the `$appearance` variable to the root view data. Override `rootView()`:

```php
public function rootView(Request $request): string
{
    return 'app';
}

public function share(Request $request): array
{
    // ... existing share code
}
```

Wait — the blade template reads `$appearance` directly, not from Inertia props. We need to pass it via view data. Add to the middleware:

```php
use Inertia\Inertia;

// In boot or register of a service provider, OR in the middleware itself:
// The cleanest way is to use Inertia's view data sharing.
```

Actually, in Inertia v3, the root view receives the appearance via `viewData`. Add to `HandleInertiaRequests`:

```php
public function rootView(Request $request): string|array
{
    return [
        'app',
        ['appearance' => $request->cookie('appearance', 'system')],
    ];
}
```

If that doesn't work in v3, use `Inertia::viewData()` in a service provider instead.

- [ ] **Step 2: Remove `csrf_token` from shared props** (Inertia v3 handles CSRF automatically)

- [ ] **Step 3: Run tests to verify backend still works**

```bash
php artisan test
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Middleware/HandleInertiaRequests.php
git commit -m "chore: update HandleInertiaRequests for Inertia v3"
```

---

## Phase 2: Core Files (types, lib, composables, entry point)

### Task 8: Rename directories to lowercase

**Files:**
- Rename: `resources/js/Components/` → `resources/js/components/`
- Rename: `resources/js/Layouts/` → `resources/js/layouts/`
- Rename: `resources/js/Pages/` → `resources/js/pages/`

- [ ] **Step 1: Rename directories**

```bash
cd resources/js
mv Components components_tmp && mv components_tmp components
mv Layouts layouts_tmp && mv layouts_tmp layouts
mv Pages pages_tmp && mv pages_tmp pages
```

(Two-step rename to handle case-insensitive filesystems on macOS.)

- [ ] **Step 2: Commit**

```bash
git add -A resources/js/
git commit -m "chore: rename frontend directories to lowercase"
```

---

### Task 9: Create types

**Files:**
- Create: `resources/js/types/auth.ts`
- Create: `resources/js/types/navigation.ts`
- Create: `resources/js/types/ui.ts`
- Create: `resources/js/types/index.ts`
- Create: `resources/js/types/global.d.ts`
- Create: `resources/js/types/vue-shims.d.ts`

- [ ] **Step 1: Create `resources/js/types/auth.ts`**

Adapted from SendKit for Lua's workspace model:

```ts
export const WorkspaceRole = {
    Admin: 'admin',
    Member: 'member',
} as const;

export type WorkspaceRole = (typeof WorkspaceRole)[keyof typeof WorkspaceRole];

export type User = {
    id: string;
    name: string;
    email: string;
    has_photo: boolean;
    photo_url: string | null;
    email_verified_at: string | null;
    current_workspace_id: string | null;
    current_workspace: Workspace | null;
    workspaces: Pick<Workspace, 'id' | 'name' | 'has_logo' | 'logo_url'>[];
    created_at: string;
    updated_at: string;
};

export type WorkspacePlan = {
    id: string;
    slug: string;
    name: string;
    link_limit: number;
    event_limit: number;
    domain_limit: number;
    member_limit: number;
    is_free: boolean;
};

export type Workspace = {
    id: string;
    name: string;
    has_logo: boolean;
    logo_url: string | null;
    created_at: string;
    plan: WorkspacePlan | null;
    subscribed: boolean;
    role: WorkspaceRole | null;
};

export type Auth = {
    user: User;
};

export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';
```

- [ ] **Step 2: Create `resources/js/types/navigation.ts`**

```ts
import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { Icon } from '@tabler/icons-vue';

export type BreadcrumbItem = {
    title: string;
    href?: string;
};

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: Icon;
    isActive?: boolean;
    activePattern?: string;
};
```

- [ ] **Step 3: Create `resources/js/types/ui.ts`**

```ts
// Re-export UI-related types used across components
export type { Appearance, ResolvedAppearance } from './auth';
```

- [ ] **Step 4: Create `resources/js/types/index.ts`**

```ts
export * from './auth';
export * from './navigation';
export * from './ui';
```

- [ ] **Step 5: Create `resources/js/types/global.d.ts`**

```ts
import type { Auth } from '@/types/auth';

declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            flash: { banner?: string; bannerStyle?: string };
            sidebarOpen: boolean;
            env: string;
            locale: string;
            [key: string]: unknown;
        };
    }
}
```

- [ ] **Step 6: Create `resources/js/types/vue-shims.d.ts`**

```ts
declare module '*.vue' {
    import type { DefineComponent } from 'vue';
    const component: DefineComponent;
    export default component;
}
```

- [ ] **Step 7: Commit**

```bash
git add resources/js/types/
git commit -m "feat: add TypeScript type definitions"
```

---

### Task 10: Create `lib/utils.ts`

**Files:**
- Create: `resources/js/lib/utils.ts`

- [ ] **Step 1: Copy from SendKit and adapt**

```ts
import type { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { toast } from 'vue-sonner';

export const cn = (...inputs: ClassValue[]) => {
    return twMerge(clsx(inputs));
};

export const toUrl = (href: NonNullable<InertiaLinkProps['href']>) => {
    return typeof href === 'string' ? href : href?.url;
};

export const formatNumber = (value: number): string => {
    return value.toLocaleString('en-US');
};

export const formatNumberCompact = (value: number): string => {
    return new Intl.NumberFormat('en-US', {
        notation: 'compact',
        compactDisplay: 'short',
        maximumFractionDigits: 1,
    }).format(value);
};

export const copyToClipboard = async (
    text: string,
    message = 'Copied to clipboard',
) => {
    try {
        await navigator.clipboard.writeText(text);
        toast.success(message);
    } catch {
        toast.error('Failed to copy to clipboard');
    }
};

export const calcPercentage = (total: number, value: number): number => {
    if (total === 0) return 0;
    return Math.round((value / total) * 100);
};

export const favicon = (url: string): string => {
    try {
        const domain = new URL(url).hostname;
        return `https://www.google.com/s2/favicons?domain=${domain}&sz=64`;
    } catch {
        return '';
    }
};
```

- [ ] **Step 2: Commit**

```bash
git add resources/js/lib/
git commit -m "feat: add lib/utils.ts with cn() and helpers"
```

---

### Task 11: Create composables

**Files:**
- Create: `resources/js/composables/useAppearance.ts`
- Create: `resources/js/composables/useCurrentUrl.ts`
- Create: `resources/js/composables/useInitials.ts`
- Create: `resources/js/composables/useUpgradeDialog.ts`

- [ ] **Step 1: Copy composables from SendKit**

```bash
mkdir -p resources/js/composables
cp ~/Code/sendkit/resources/js/composables/useAppearance.ts resources/js/composables/
cp ~/Code/sendkit/resources/js/composables/useCurrentUrl.ts resources/js/composables/
cp ~/Code/sendkit/resources/js/composables/useInitials.ts resources/js/composables/
cp ~/Code/sendkit/resources/js/composables/useUpgradeDialog.ts resources/js/composables/
```

These need no adaptation — they use generic patterns.

- [ ] **Step 2: Commit**

```bash
git add resources/js/composables/
git commit -m "feat: add composables from SendKit"
```

---

### Task 12: Convert utility files to TypeScript

**Files:**
- Rename+convert: `resources/js/bootstrap.js` → `resources/js/bootstrap.ts`
- Rename+convert: `resources/js/date.js` → `resources/js/date.ts`
- Rename+convert: `resources/js/dayjs.js` → `resources/js/dayjs.ts`
- Rename+convert: `resources/js/debounce.js` → `resources/js/debounce.ts`
- Rename+convert: `resources/js/country.js` → `resources/js/country.ts`
- Rewrite: `resources/js/echo.js` → `resources/js/echo.ts`
- Delete: `resources/js/helper.js` (moved to lib/utils.ts)
- Delete: `resources/js/theme.js` (replaced by useAppearance.ts)

- [ ] **Step 1: Rename files**

```bash
cd resources/js
mv bootstrap.js bootstrap.ts
mv date.js date.ts
mv dayjs.js dayjs.ts
mv debounce.js debounce.ts
mv country.js country.ts
```

- [ ] **Step 2: Rewrite `echo.ts`**

Replace contents with SendKit's pattern:

```ts
import { configureEcho } from '@laravel/echo-vue';

configureEcho({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

- [ ] **Step 3: Delete old files**

```bash
rm resources/js/helper.js resources/js/theme.js resources/js/echo.js
```

- [ ] **Step 4: Commit**

```bash
git add -A resources/js/
git commit -m "chore: convert utility files to TypeScript"
```

---

### Task 13: Create `app.ts` entry point

**Files:**
- Delete: `resources/js/app.js`
- Create: `resources/js/app.ts`

- [ ] **Step 1: Create `resources/js/app.ts`**

```ts
import '../css/app.css';
import './echo';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

// Third-party plugins still needed
import { i18nVue } from 'laravel-vue-i18n';
import VCalendar from 'v-calendar';
import 'v-calendar/style.css';
import Vue3ColorPicker from 'vue3-colorpicker';
import 'vue3-colorpicker/style.css';

createInertiaApp({
    title: (title) => `${title} - ${import.meta.env.VITE_APP_NAME || 'Lua'}`,
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VCalendar)
            .use(i18nVue, {
                resolve: async (lang: string) => {
                    const langs = import.meta.glob<{ default: Record<string, string> }>('../../lang/*.json');
                    return await langs[`../../lang/php_${lang}.json`]();
                },
            })
            .use(Vue3ColorPicker)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
```

- [ ] **Step 2: Delete old entry point**

```bash
rm resources/js/app.js
```

- [ ] **Step 3: Commit**

```bash
git add resources/js/app.ts
git commit -m "feat: create TypeScript entry point"
```

---

## Phase 3: UI Components (shadcn + core components)

### Task 14: Copy all shadcn ui/ components from SendKit

**Files:**
- Create: `resources/js/components/ui/` (~207 files)

- [ ] **Step 1: Copy entire ui/ directory**

```bash
mkdir -p resources/js/components/ui
cp -R ~/Code/sendkit/resources/js/components/ui/* resources/js/components/ui/
```

- [ ] **Step 2: Verify all component directories are present**

```bash
ls resources/js/components/ui/
```

Expected: alert, alert-dialog, avatar, badge, breadcrumb, button, button-group, calendar, card, chart, checkbox, collapsible, combobox, date-range-picker, dialog, dropdown-menu, input, input-otp, label, native-select, navigation-menu, popover, range-calendar, select, separator, sheet, sidebar, skeleton, slider, sonner, spinner, switch, table, tabs, tooltip

- [ ] **Step 3: Commit**

```bash
git add resources/js/components/ui/
git commit -m "feat: add shadcn-vue UI components from SendKit"
```

---

### Task 15: Copy core components from SendKit

**Files:**
- Create: `resources/js/components/AppSidebar.vue`
- Create: `resources/js/components/AppSidebarHeader.vue`
- Create: `resources/js/components/Breadcrumbs.vue`
- Create: `resources/js/components/ConfirmDeleteModal.vue`
- Create: `resources/js/components/EmptyState.vue`
- Create: `resources/js/components/Heading.vue`
- Create: `resources/js/components/NavMain.vue`
- Create: `resources/js/components/NavUser.vue`
- Create: `resources/js/components/PageHeader.vue`
- Create: `resources/js/components/TextLink.vue`
- Create: `resources/js/components/Toast.vue`
- Create: `resources/js/components/UserInfo.vue`
- Create: `resources/js/components/UserMenuContent.vue`

- [ ] **Step 1: Copy components that need NO adaptation**

```bash
cd resources/js/components
cp ~/Code/sendkit/resources/js/components/AppSidebarHeader.vue .
cp ~/Code/sendkit/resources/js/components/Breadcrumbs.vue .
cp ~/Code/sendkit/resources/js/components/ConfirmDeleteModal.vue .
cp ~/Code/sendkit/resources/js/components/EmptyState.vue .
cp ~/Code/sendkit/resources/js/components/Heading.vue .
cp ~/Code/sendkit/resources/js/components/NavMain.vue .
cp ~/Code/sendkit/resources/js/components/NavUser.vue .
cp ~/Code/sendkit/resources/js/components/PageHeader.vue .
cp ~/Code/sendkit/resources/js/components/TextLink.vue .
cp ~/Code/sendkit/resources/js/components/Toast.vue .
cp ~/Code/sendkit/resources/js/components/UserInfo.vue .
cp ~/Code/sendkit/resources/js/components/UserMenuContent.vue .
```

- [ ] **Step 2: Adapt `UserMenuContent.vue`**

Remove SendKit-specific items (posthog, onboarding route). Keep:
- Account link (point to Lua's setting.account.edit)
- Theme switcher (light/dark/system)
- Logout

Remove these imports and usages:
- `import posthog from '@/posthog'`
- `import { index as onboarding } from '@/routes/app/onboarding'`
- The onboarding DropdownMenuItem
- The `handleLogout` posthog.reset() call

Replace route imports with Wayfinder routes (will be generated after `php artisan wayfinder:generate`). For now, use string hrefs as placeholders that we'll fix once Wayfinder generates.

- [ ] **Step 3: Adapt `NavMain.vue`**

This component works as-is — it receives items as props. No SendKit-specific code. Remove `useUpgradeDialog` if Lua doesn't have the upgrade locked feature concept. Actually, keep it — Lua has billing/upgrade too.

- [ ] **Step 4: Commit**

```bash
git add resources/js/components/*.vue
git commit -m "feat: add core components from SendKit"
```

---

### Task 16: Create `AppSidebar.vue` for Lua

**Files:**
- Create: `resources/js/components/AppSidebar.vue`

- [ ] **Step 1: Write AppSidebar adapted for Lua's navigation**

Copy the structure from SendKit's `AppSidebar.vue` but replace nav items with Lua's menu:

```vue
<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    IconChartBar,
    IconChevronRight,
    IconLink,
    IconPlus,
    IconSettings,
    IconTimeline,
} from '@tabler/icons-vue';
import { computed } from 'vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Avatar } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';

const page = usePage();
const auth = computed(() => page.props.auth);

const mainNavItems: NavItem[] = [
    {
        title: 'Analytics',
        href: '/analytics',
        icon: IconChartBar,
    },
    {
        title: 'Links',
        href: '/links',
        icon: IconLink,
    },
    {
        title: 'Events',
        href: '/events',
        icon: IconTimeline,
    },
    {
        title: 'Settings',
        href: '/settings/workspace',
        icon: IconSettings,
        activePattern: '/settings',
    },
];

const switchWorkspace = (workspaceId: string) => {
    router.put(
        '/workspaces/update-current',
        { workspace_id: workspaceId },
        { preserveScroll: true },
    );
};
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <SidebarMenuButton
                                size="lg"
                                class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                            >
                                <Avatar
                                    :src="auth.user?.current_workspace?.logo_url"
                                    :name="auth.user?.current_workspace?.name ?? '?'"
                                    class="h-8 w-8 shrink-0 rounded-lg"
                                    fallback-class="bg-sidebar-accent text-sidebar-accent-foreground"
                                />
                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">
                                        {{ auth.user?.current_workspace?.name ?? 'Select workspace' }}
                                    </span>
                                </div>
                                <IconChevronRight class="ml-auto size-4" />
                            </SidebarMenuButton>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            class="w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                            align="start"
                            side="right"
                            :side-offset="4"
                        >
                            <DropdownMenuLabel class="text-xs text-muted-foreground">
                                Workspaces
                            </DropdownMenuLabel>
                            <div class="space-y-0.5">
                                <DropdownMenuItem
                                    v-for="workspace in auth.user?.workspaces"
                                    :key="workspace.id"
                                    class="gap-2"
                                    :class="workspace.id === auth.user?.current_workspace?.id ? 'bg-accent' : ''"
                                    @click="switchWorkspace(workspace.id)"
                                >
                                    <Avatar
                                        :src="workspace.logo_url"
                                        :name="workspace.name"
                                        class="h-5 w-5 shrink-0 rounded-md"
                                        fallback-class="text-[10px]"
                                    />
                                    {{ workspace.name }}
                                </DropdownMenuItem>
                            </div>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem as-child>
                                <Link href="/workspaces/create">
                                    <IconPlus class="size-4" />
                                    Create Workspace
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
```

Note: Route hrefs use string paths for now. These will be replaced with Wayfinder-generated route helpers once we run `php artisan wayfinder:generate` and the routes/ directory is created.

- [ ] **Step 2: Commit**

```bash
git add resources/js/components/AppSidebar.vue
git commit -m "feat: create AppSidebar with Lua navigation"
```

---

### Task 17: Create `AppLogo.vue` and `AppLogoIcon.vue`

**Files:**
- Create: `resources/js/components/AppLogo.vue`
- Create: `resources/js/components/AppLogoIcon.vue`

- [ ] **Step 1: Create logo components**

These are Lua-specific. Check what the current `ApplicationLogo.vue` has and adapt.

`AppLogo.vue`:
```vue
<script setup lang="ts">
</script>

<template>
    <div class="flex items-center gap-2">
        <AppLogoIcon class="size-8" />
        <span class="text-lg font-semibold">Lua</span>
    </div>
</template>
```

`AppLogoIcon.vue`:
```vue
<script setup lang="ts">
</script>

<template>
    <img src="/favicon.ico" alt="Lua" class="size-6" />
</template>
```

(Adapt based on what Lua actually has for its logo asset.)

- [ ] **Step 2: Commit**

```bash
git add resources/js/components/AppLogo.vue resources/js/components/AppLogoIcon.vue
git commit -m "feat: add Lua logo components"
```

---

## Phase 4: Layouts

### Task 18: Create layouts

**Files:**
- Create: `resources/js/layouts/AppLayout.vue`
- Create: `resources/js/layouts/app/AppSidebarLayout.vue`
- Create: `resources/js/layouts/AuthLayout.vue`
- Create: `resources/js/layouts/auth/AuthCardLayout.vue`
- Create: `resources/js/layouts/settings/Layout.vue`

- [ ] **Step 1: Copy and adapt AppSidebarLayout from SendKit**

```bash
mkdir -p resources/js/layouts/app resources/js/layouts/auth resources/js/layouts/settings
cp ~/Code/sendkit/resources/js/layouts/AppLayout.vue resources/js/layouts/
cp ~/Code/sendkit/resources/js/layouts/app/AppSidebarLayout.vue resources/js/layouts/app/
cp ~/Code/sendkit/resources/js/layouts/AuthLayout.vue resources/js/layouts/
cp ~/Code/sendkit/resources/js/layouts/auth/AuthCardLayout.vue resources/js/layouts/auth/
cp ~/Code/sendkit/resources/js/layouts/settings/Layout.vue resources/js/layouts/settings/
```

- [ ] **Step 2: Adapt `AppSidebarLayout.vue`**

Remove SendKit-specific code:
- Remove `isSendingPaused` alert
- Remove `PlanPickerDialog` (or adapt for Lua's billing)
- Remove `TeamRole` import
- Keep: `SidebarProvider`, `AppSidebar`, `AppSidebarHeader`, `SidebarInset`, `Toast`

- [ ] **Step 3: Create `SettingsNav.vue`**

Adapt from SendKit's `SettingsNav.vue` with Lua's settings pages:

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { type NavItem } from '@/types';

const navItems: NavItem[] = [
    { title: 'Workspace', href: '/settings/workspace' },
    { title: 'Domains', href: '/settings/domains' },
    { title: 'Tags', href: '/settings/tags' },
    { title: 'API Tokens', href: '/settings/api-tokens' },
    { title: 'Billing', href: '/settings/billing' },
    { title: 'Team Members', href: '/settings/users' },
];

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <nav class="inline-flex h-9 w-fit items-center justify-center rounded-lg bg-muted p-1 text-muted-foreground">
        <Link
            v-for="item in navItems"
            :key="toUrl(item.href)"
            :href="item.href"
            class="inline-flex items-center justify-center rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap transition-all focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50"
            :class="isCurrentUrl(item.href) ? 'bg-background text-foreground shadow-sm' : 'hover:bg-background/50 hover:text-foreground'"
        >
            {{ item.title }}
        </Link>
    </nav>
</template>
```

- [ ] **Step 4: Adapt `AuthCardLayout.vue`**

Replace SendKit branding with Lua branding. Remove code highlighting (shiki), remove SendKit-specific slides. Keep the two-column layout structure with a simpler right panel for Lua.

- [ ] **Step 5: Commit**

```bash
git add resources/js/layouts/ resources/js/components/SettingsNav.vue
git commit -m "feat: add layouts from SendKit"
```

---

## Phase 5: Migrate Pages (the bulk of the work)

### Task 19: Generate Wayfinder routes

- [ ] **Step 1: Generate routes**

```bash
php artisan wayfinder:generate
```

This creates `resources/js/routes/` with typed route helpers.

- [ ] **Step 2: Verify generated routes**

```bash
ls resources/js/routes/
```

- [ ] **Step 3: Commit**

```bash
git add resources/js/routes/
git commit -m "feat: generate Wayfinder route helpers"
```

---

### Task 20: Migrate Auth pages

**Files:**
- Rewrite: `resources/js/pages/Auth/Login.vue`
- Rewrite: `resources/js/pages/Auth/Register.vue`
- Rewrite: `resources/js/pages/Auth/ForgotPassword.vue`
- Rewrite: `resources/js/pages/Auth/ResetPassword.vue`
- Rewrite: `resources/js/pages/Auth/VerifyEmail.vue`
- Rewrite: `resources/js/pages/Auth/ConfirmPassword.vue`
- Rewrite: `resources/js/pages/Auth/Invitation.vue`
- Rewrite: `resources/js/pages/Auth/Partial/Social.vue`

For each file:
1. Add `<script setup lang="ts">`
2. Replace `defineProps({...})` with `defineProps<{...}>()`
3. Replace Phosphor icons with Tabler icons
4. Replace `route('...')` with Wayfinder route helpers or string hrefs
5. Use `AuthLayout` instead of old `Auth` layout
6. Replace custom form inputs with shadcn `<Input>`, `<Label>`, `<Button>`

- [ ] **Step 1: Migrate each auth page one by one**

Read each current file, rewrite in TS with shadcn components. Use SendKit's auth pages as reference for the layout pattern (they all use `AuthLayout`).

- [ ] **Step 2: Run build to check for errors**

```bash
npx vite build 2>&1 | tail -20
```

- [ ] **Step 3: Commit**

```bash
git add resources/js/pages/Auth/
git commit -m "feat: migrate auth pages to TypeScript + shadcn"
```

---

### Task 21: Migrate Link pages

**Files:**
- Rewrite: `resources/js/pages/Link/Index.vue`
- Rewrite: `resources/js/pages/Link/Create.vue`
- Rewrite: `resources/js/pages/Link/Edit.vue`
- Rewrite: `resources/js/pages/Link/Password.vue`

Key changes per file:
- Layout: `Master` → `AppLayout`
- `<Modal>` → `<Dialog>` from shadcn
- `<SlideOver>` → `<Sheet>` from shadcn
- `<Dropdown>` (HeadlessUI Listbox for domain select) → `<Select>` from shadcn
- `<Dropdown>` (HeadlessUI Listbox for tags with search) → `<Combobox>` from shadcn
- All `<Ph*>` icons → `<Icon*>` from tabler
- `.btn`, `.btn-primary` classes → `<Button>` component with variants
- `.badge` classes → `<Badge>` component
- Custom `<Table>` → shadcn `<Table>`
- `<Banner>` toast → handled by `Toast.vue` (vue-sonner) in layout

**Icon mapping for Link pages:**
- `PhCursorClick` → `IconClick`
- `PhGear` → `IconSettings`

- [ ] **Step 1: Migrate each link page**
- [ ] **Step 2: Build check**
- [ ] **Step 3: Commit**

```bash
git add resources/js/pages/Link/
git commit -m "feat: migrate link pages to TypeScript + shadcn"
```

---

### Task 22: Migrate Analytics pages

**Files:**
- Rewrite all 20 files in `resources/js/pages/Analytics/`

Key changes:
- Layout: `Master` → `AppLayout`
- `<Tab>` → `<Tabs>` from shadcn
- `<Table>` → shadcn `<Table>`
- `<RangePicker>` → shadcn `<DateRangePicker>`
- Keep `Chart.vue` (chart.js) but convert to TS

- [ ] **Step 1: Migrate each analytics page**
- [ ] **Step 2: Build check**
- [ ] **Step 3: Commit**

```bash
git add resources/js/pages/Analytics/
git commit -m "feat: migrate analytics pages to TypeScript + shadcn"
```

---

### Task 23: Migrate Event pages

**Files:**
- Rewrite: `resources/js/pages/Event/Index.vue`
- Rewrite: `resources/js/pages/Event/Header.vue`
- Rewrite: `resources/js/pages/Event/ChartClick.vue`
- Rewrite: `resources/js/pages/Event/ChartQR.vue`

- [ ] **Step 1: Migrate each event page**
- [ ] **Step 2: Build check**
- [ ] **Step 3: Commit**

```bash
git add resources/js/pages/Event/
git commit -m "feat: migrate event pages to TypeScript + shadcn"
```

---

### Task 24: Migrate Settings pages

**Files:**
- Rewrite all 17 files in `resources/js/pages/Setting/`

Key changes:
- Use `AppLayout` with `settings/Layout.vue` nested
- `SettingsNav` for tab navigation
- Replace all form components with shadcn equivalents
- `<Toggle>` → `<Switch>`
- Domain status badges → `<Badge>` with variants
- All modals → shadcn `<Dialog>` or `<AlertDialog>`
- `<Select>` (role picker) → shadcn `<Select>`

- [ ] **Step 1: Migrate Account pages (Edit.vue, Avatar.vue)**
- [ ] **Step 2: Migrate Workspace pages (Edit.vue, Logo.vue)**
- [ ] **Step 3: Migrate Domain pages (Index.vue, Create.vue, Edit.vue)**
- [ ] **Step 4: Migrate Tag pages (Index.vue, Create.vue, Edit.vue)**
- [ ] **Step 5: Migrate ApiToken pages (Index.vue, Create.vue)**
- [ ] **Step 6: Migrate Billing pages (Index.vue, Upgrade.vue, Success.vue)**
- [ ] **Step 7: Migrate TeamMember pages (Index.vue, Invite/Create.vue, Invite/Index.vue)**
- [ ] **Step 8: Build check**
- [ ] **Step 9: Commit**

```bash
git add resources/js/pages/Setting/
git commit -m "feat: migrate settings pages to TypeScript + shadcn"
```

---

### Task 25: Migrate remaining pages

**Files:**
- Rewrite: `resources/js/pages/Workspace/Create.vue`
- Rewrite: `resources/js/pages/Error.vue`

- [ ] **Step 1: Migrate each page**
- [ ] **Step 2: Build check**
- [ ] **Step 3: Commit**

```bash
git add resources/js/pages/
git commit -m "feat: migrate remaining pages to TypeScript + shadcn"
```

---

## Phase 6: Cleanup and Verification

### Task 26: Delete old components and files

**Files to delete:**
- `resources/js/components/Accordion.vue` (replaced by Collapsible)
- `resources/js/components/ActionSection.vue`
- `resources/js/components/Announcement.vue`
- `resources/js/components/ApplicationLogo.vue` (replaced by AppLogo)
- `resources/js/components/Banner.vue` (replaced by Toast/Sonner)
- `resources/js/components/Button.vue` (replaced by shadcn Button)
- `resources/js/components/Checkbox.vue` (replaced by shadcn Checkbox)
- `resources/js/components/ConfirmationModal.vue` (replaced by AlertDialog)
- `resources/js/components/DatePicker.vue` (replaced by shadcn DatePicker)
- `resources/js/components/DialogModal.vue` (replaced by shadcn Dialog)
- `resources/js/components/Dropdown.vue` (replaced by Select/Combobox)
- `resources/js/components/FormSection.vue`
- `resources/js/components/Input.vue` (replaced by shadcn Input)
- `resources/js/components/InputError.vue` (rewrite or keep as TS)
- `resources/js/components/InputHelp.vue`
- `resources/js/components/Label.vue` (replaced by shadcn Label)
- `resources/js/components/Modal.vue` (replaced by shadcn Dialog)
- `resources/js/components/Pagination.vue` (rewrite with shadcn Button)
- `resources/js/components/Radio.vue`
- `resources/js/components/RangePicker.vue` (replaced by DateRangePicker)
- `resources/js/components/Sandbox.vue`
- `resources/js/components/SectionBorder.vue` (replaced by Separator)
- `resources/js/components/SectionTitle.vue` (replaced by Heading)
- `resources/js/components/Select.vue` (replaced by shadcn Select)
- `resources/js/components/SlideOver.vue` (replaced by Sheet)
- `resources/js/components/Tab.vue` (replaced by Tabs)
- `resources/js/components/Table.vue` (replaced by shadcn Table)
- `resources/js/components/Tag.vue` (replaced by Badge)
- `resources/js/components/Textarea.vue`
- `resources/js/components/Toggle.vue` (replaced by Switch)
- `resources/js/components/UserAvatar.vue` (replaced by Avatar)
- Old layouts: `resources/js/layouts/Sidebar.vue`, `resources/js/layouts/Components/`

- [ ] **Step 1: Delete old component files**

```bash
rm resources/js/components/Accordion.vue resources/js/components/ActionSection.vue resources/js/components/Announcement.vue resources/js/components/ApplicationLogo.vue resources/js/components/Banner.vue resources/js/components/Button.vue resources/js/components/Checkbox.vue resources/js/components/ConfirmationModal.vue resources/js/components/DatePicker.vue resources/js/components/DialogModal.vue resources/js/components/Dropdown.vue resources/js/components/FormSection.vue resources/js/components/Input.vue resources/js/components/InputError.vue resources/js/components/InputHelp.vue resources/js/components/Label.vue resources/js/components/Modal.vue resources/js/components/Radio.vue resources/js/components/RangePicker.vue resources/js/components/Sandbox.vue resources/js/components/SectionBorder.vue resources/js/components/SectionTitle.vue resources/js/components/Select.vue resources/js/components/SlideOver.vue resources/js/components/Tab.vue resources/js/components/Table.vue resources/js/components/Tag.vue resources/js/components/Textarea.vue resources/js/components/Toggle.vue resources/js/components/UserAvatar.vue
```

- [ ] **Step 2: Delete old layouts**

```bash
rm -rf resources/js/layouts/Sidebar.vue resources/js/layouts/Master.vue resources/js/layouts/Auth.vue resources/js/layouts/Components/
```

- [ ] **Step 3: Commit**

```bash
git add -A
git commit -m "chore: remove old HeadlessUI components and layouts"
```

---

### Task 27: Final build and type check

- [ ] **Step 1: Run TypeScript check**

```bash
npx vue-tsc --noEmit
```

Fix any type errors.

- [ ] **Step 2: Run Vite build**

```bash
npx vite build
```

Fix any build errors.

- [ ] **Step 3: Run backend tests**

```bash
php artisan test
```

All 73 tests should still pass.

- [ ] **Step 4: Final commit**

```bash
git add -A
git commit -m "chore: fix build and type errors"
```

---

### Task 28: Manual testing checklist

- [ ] **Step 1: Test auth flow** — login, register, forgot password, reset password
- [ ] **Step 2: Test main app** — sidebar navigation, workspace switching
- [ ] **Step 3: Test links** — create, edit, delete, table, pagination
- [ ] **Step 4: Test analytics** — charts, tabs, date range picker
- [ ] **Step 5: Test events** — event list, filters
- [ ] **Step 6: Test settings** — all settings pages, domain management, tags, API tokens, billing, team members
- [ ] **Step 7: Test dark mode** — toggle light/dark/system, verify all pages
- [ ] **Step 8: Test mobile** — sidebar collapse, responsive layout
- [ ] **Step 9: Test toasts** — flash messages on form submissions

---

## Phosphor → Tabler Icon Mapping Reference

| Phosphor | Tabler | Used in |
|---|---|---|
| PhArrowRight | IconArrowRight | EmptyState |
| PhBookOpen | IconBook | UserDropdown |
| PhCaretDown | IconChevronDown | Accordion, RangePicker, UserDropdown, Source |
| PhCaretRight | IconChevronRight | Usage |
| PhCaretUp | IconChevronUp | UserDropdown |
| PhCheck | IconCheck | UserDropdown |
| PhCheckCircle | IconCircleCheck | Event/Header, Billing/Upgrade |
| PhCircle | IconCircle | Event/Header |
| PhCursorClick | IconClick | Usage, Link/Index, Billing |
| PhDotsThreeOutline | IconDots | TeamMember |
| PhDotsSixVertical | IconGripVertical | Tag |
| PhGear | IconSettings | Event/Header, Tag |
| PhGlobe | IconWorld | Billing |
| PhInfo | IconInfoCircle | Label, Billing |
| PhKey | IconKey | ApiToken |
| PhLink | IconLink | Usage, Billing |
| PhList | IconMenu2 | Sidebar |
| PhMagnifyingGlass | IconSearch | TeamMember |
| PhPlus | IconPlus | UserDropdown |
| PhPower | IconLogout | UserDropdown |
| PhQuestion | IconHelp | UserDropdown |
| PhShoppingBag | IconShoppingBag | UserDropdown |
| PhSpinnerGap | IconLoader2 | Billing/Success |
| PhStorefront | IconBuildingStore | UserDropdown |
| PhTag | IconTag | Billing |
| PhTrash | IconTrash | Avatar, Logo |
| PhUser | IconUser | UserDropdown |
| PhUsers | IconUsers | Billing |
| PhX | IconX | DatePicker, Modal, SlideOver, Sidebar, ApiToken, Tag |

---

## Route Migration Reference

All `route('name')` calls need to be replaced with Wayfinder-generated helpers or string paths. The full list of ~52 unique route names is documented in the spec. After `php artisan wayfinder:generate`, import from `@/routes/` directory.
