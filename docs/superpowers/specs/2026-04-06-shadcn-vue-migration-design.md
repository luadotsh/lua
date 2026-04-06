# Shadcn-Vue Migration Design Spec

**Date:** 2026-04-06
**Branch:** `feature/shadcn-vue-migration`
**Approach:** Big Bang — full migration in one branch

## Goal

Migrate the entire Lua frontend from HeadlessUI + custom JS components to shadcn-vue (reka-ui) + TypeScript, replicating the UI architecture of the SendKit project (`~/Code/sendkit`).

## Scope

### What changes

1. **Language**: All `.vue` and `.js` files → TypeScript (`.ts`, `<script setup lang="ts">`)
2. **UI Library**: HeadlessUI v1 → reka-ui (shadcn-vue, new-york-v4 style)
3. **Icons**: @phosphor-icons/vue → @tabler/icons-vue
4. **Routing**: Ziggy → Wayfinder (Inertia v3 type-safe routes)
5. **Inertia**: v2 → v3
6. **Toast**: Custom Banner.vue → vue-sonner (Sonner component from shadcn)
7. **Entry point**: `app.js` → `app.ts`
8. **Layouts**: Custom layouts → SendKit-style AppSidebarLayout
9. **Components**: 37 custom components → shadcn ui/ components + custom wrappers
10. **CSS**: Replace `app.css` entirely with SendKit's design token system (CSS variables, `@theme inline`, shadows, radii)
11. **Vite**: Update `vite.config.js` → `vite.config.ts` with Wayfinder plugin
12. **Blade**: Update `app.blade.php` — add dark mode detection script (from SendKit), update font (Inter → Inter or keep), point to `app.ts`, remove `@routes` (Ziggy)
13. **Directory casing**: `Components/` → `components/`, `Layouts/` → `layouts/`, `Pages/` → `pages/` (match SendKit lowercase convention)
14. **Select logic**: All plain selects → shadcn `<Select>`, selects with search → shadcn `<Combobox>`
15. **Settings nav**: Copy `SettingsNav.vue` from SendKit, adapt for Lua settings pages
16. **User menu**: Copy `UserInfo.vue` + `UserMenuContent.vue` from SendKit (includes theme switcher light/dark/system)

### What stays the same

- Laravel backend (controllers, routes, middleware) — unchanged
- Inertia page structure (Pages/ directory organization)
- Business logic in pages (form submissions, data fetching)
- Third-party libs that still apply: chart.js, dayjs, v-calendar, qrcode, vuedraggable, vue3-colorpicker
- Dark mode support (class-based)
- i18n (laravel-vue-i18n)
- Real-time (Laravel Echo)

### What gets removed

- @headlessui/vue
- @phosphor-icons/vue
- floating-vue
- tightenco/ziggy (JS package — remove from both npm and composer)
- Custom CSS component classes in app.css (.btn, .btn-primary, .card, .badge, .form-input, .table-*, etc.)
- `resources/js/theme.js` (replaced by `composables/useAppearance.ts`)
- `resources/js/helper.js` (utilities moved to `lib/utils.ts`)
- `resources/css/v-calendar.css` and `resources/css/floating.css` (no longer needed)

### Components from SendKit NOT being copied (domain-specific)

- `editor/` (tiptap email editor — 30+ files)
- `automations/` (workflow builder — 12 files)
- `campaigns/`, `contacts/`, `contact/`, `contact-property/`, `domain/`, `email/`, `email-validation/`, `metrics/`, `segment/`, `sender/`, `template/`, `webhook/`, `api-key/`
- `AutoSaveStatus.vue`, `SocialLoginButtons.vue`, `PlaceholderPattern.vue`

## Dependencies

### Add (from SendKit)

```json
{
  "dependencies": {
    "@inertiajs/vue3": "^3.0.0",
    "@tabler/icons-vue": "^3.37.1",
    "@vueuse/core": "^12.8.2",
    "class-variance-authority": "^0.7.1",
    "clsx": "^2.1.1",
    "reka-ui": "^2.9.0",
    "tailwind-merge": "^3.2.0",
    "tw-animate-css": "^1.2.5",
    "vue-sonner": "^2.0.9"
  },
  "devDependencies": {
    "@laravel/echo-vue": "^2.3.0",
    "@laravel/vite-plugin-wayfinder": "^0.1.3",
    "@types/node": "^22.13.5",
    "typescript": "^5.2.2",
    "vue-tsc": "^2.2.4",
    "laravel-vite-plugin": "^2.0.0",
    "vite": "^7.0.4",
    "@vitejs/plugin-vue": "^6.0.0"
  }
}
```

**Composer:**
```
composer require inertiajs/inertia-laravel:^3.0 laravel/wayfinder
composer remove tightenco/ziggy
```

### Remove (npm)

```
@headlessui/vue
@phosphor-icons/vue
floating-vue
laravel-echo (replaced by @laravel/echo-vue)
pusher-js (bundled in echo-vue)
autoprefixer (if still present)
```

### Keep (despite Inertia v3 removing it)

```
axios — used directly in 5 files (Menu.vue, Logo.vue, Avatar.vue, Qrcode.vue, bootstrap.js)
```

## Architecture

### Directory Structure (target)

```
resources/js/
  app.ts                          # Entry point (TypeScript)
  bootstrap.ts                    # Axios + Echo setup
  date.ts                         # Date utilities
  dayjs.ts                        # Dayjs config
  debounce.ts                     # Debounce utility
  echo.ts                         # Laravel Echo config
  country.ts                      # Country data
  components/
    ui/                           # Copied from SendKit — shadcn/reka-ui
      alert/
      alert-dialog/
      avatar/
      badge/
      breadcrumb/
      button/
      calendar/
      card/
      checkbox/
      collapsible/
      combobox/
      date-range-picker/
      dialog/
      dropdown-menu/
      input/
      label/
      native-select/
      popover/
      range-calendar/
      select/
      separator/
      sheet/
      sidebar/
      skeleton/
      slider/
      sonner/
      spinner/
      switch/
      table/
      tabs/
      tooltip/
    AppHeader.vue                 # From SendKit
    AppLogo.vue                   # Lua-specific logo
    AppLogoIcon.vue               # Lua-specific icon
    AppSidebar.vue                # From SendKit, adapted for Lua nav
    AppSidebarHeader.vue          # From SendKit
    Breadcrumbs.vue               # From SendKit
    ConfirmDeleteModal.vue        # Rewritten using shadcn Dialog
    DatePicker.vue                # From SendKit
    EmptyState.vue                # Rewritten using shadcn primitives
    Heading.vue                   # From SendKit
    InputError.vue                # Rewritten in TS
    NavFooter.vue                 # From SendKit, adapted for Lua
    NavMain.vue                   # From SendKit, adapted for Lua menu items
    NavUser.vue                   # From SendKit, adapted
    PageHeader.vue                # From SendKit
    TextLink.vue                  # From SendKit
    Toast.vue                     # From SendKit (vue-sonner wrapper)
    PhotoUpload.vue               # From SendKit
    PlanPickerDialog.vue          # From SendKit, adapted for Lua plans
    UserInfo.vue                  # From SendKit (avatar + name display)
    UserMenuContent.vue           # From SendKit (dropdown with theme switcher)
    SettingsNav.vue               # From SendKit, adapted for Lua settings pages
    InputError.vue                # Rewritten in TS
    Chart.vue                     # Keep existing (chart.js)
    ColorSelector.vue             # Keep existing (vue3-colorpicker)
    DomainStatus.vue              # Rewrite in TS
    Pagination.vue                # Rewrite using shadcn Button
    Qrcode.vue                    # Rewrite in TS
    RangePicker.vue               # Replace with shadcn DateRangePicker
    Tab.vue                       # Replace with shadcn Tabs
    Table.vue                     # Rewrite using shadcn Table
    Tag.vue                       # Rewrite using shadcn Badge
    Toggle.vue                    # Replace with shadcn Switch
  composables/
    useAppearance.ts              # From SendKit (dark/light theme)
    useInitials.ts                # From SendKit
    useCurrentUrl.ts              # From SendKit
    useFeatureAccess.ts           # From SendKit, adapted for Lua plans
    useUpgradeDialog.ts           # From SendKit, adapted
  layouts/
    AppLayout.vue                 # From SendKit
    AppSidebarLayout.vue          # From SendKit
    AuthLayout.vue                # From SendKit
    AuthCardLayout.vue            # From SendKit
    settings/
      Layout.vue                  # From SendKit
  lib/
    utils.ts                      # From SendKit (cn() helper, etc.)
  pages/                          # All existing pages rewritten in TS
    Analytics/
    Auth/
    Event/
    Link/
    Setting/
    Workspace/
    Error.vue
  types/
    auth.ts                       # From SendKit, adapted
    billing.ts                    # From SendKit, adapted
    global.d.ts                   # From SendKit
    index.ts                      # Lua-specific types
    navigation.ts                 # From SendKit, adapted
    ui.ts                         # From SendKit
    vue-shims.d.ts                # From SendKit
```

### CSS Strategy (`resources/css/app.css`)

Replace entirely with SendKit's approach:

```css
@import 'tailwindcss';
@import 'tw-animate-css';
@import 'vue-sonner/style.css';
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@custom-variant dark (&:is(.dark *));

:root { /* SendKit's design tokens — all CSS custom properties */ }
.dark { /* Dark mode overrides */ }
@theme inline { /* Tailwind theme mapping to CSS vars */ }
@layer base { /* Border + body defaults */ }
```

- Copy all CSS custom properties from SendKit (colors, shadows, radii, fonts, spacing)
- Adapt font-family: change 'Space Grotesk' to 'Inter' (Lua's brand font)
- Adapt sidebar-primary color to Lua's violet brand if desired
- Remove ALL existing custom classes (.btn-*, .card, .badge-*, .form-input, .table-*, .link, .page-title, etc.)
- Keep: `.prose-formatter`, tiptap paragraph fix, vue3-colorpicker overrides

### Blade Template (`resources/views/app.blade.php`)

Copy from SendKit and adapt:
- Add inline dark mode detection `<script>` in `<head>` (prevents FOUC)
- Add `@class(['dark' => ($appearance ?? 'system') == 'dark'])` on `<html>` tag
- Change `@routes` (Ziggy) → remove entirely
- Change `@vite('resources/js/app.js')` → `@vite(['resources/js/app.ts'])`
- Keep Inter font (Lua's brand) instead of Space Grotesk
- Remove `class="h-full"` from html/body (SendKit doesn't use it)

### Layout Migration

**Current Lua:**
- `Master.vue` — main layout with custom Sidebar
- `Auth.vue` — auth pages
- `Sidebar.vue` — HeadlessUI Dialog for mobile, static for desktop

**Target (from SendKit):**
- `AppLayout.vue` → wraps `AppSidebarLayout.vue`
- `AppSidebarLayout.vue` → uses shadcn `SidebarProvider` + `SidebarInset`
- `AppSidebar.vue` → uses shadcn Sidebar components (collapsible groups, menu items)
- `AuthLayout.vue` → wraps `AuthCardLayout.vue`

The sidebar navigation items will be adapted for Lua's menu:
- Analytics
- Links
- Events
- Settings (Workspace, Domains, Tags, API Tokens, Billing, Team Members)

### Icon Migration

All `<Ph*>` icon components → `<Icon*>` from @tabler/icons-vue.

Mapping approach: find equivalent Tabler icon for each Phosphor icon used. Where no exact match exists, pick the closest alternative.

### Route Migration (Ziggy → Wayfinder)

**Current:** `route('links.index')` via Ziggy
**Target:** `route('links.index')` via Wayfinder (type-safe, auto-generated)

Steps:
1. Install `@laravel/vite-plugin-wayfinder`
2. Add Wayfinder plugin to `vite.config.ts`
3. Run `php artisan wayfinder:generate`
4. Replace Ziggy's `route()` with Wayfinder's generated route helpers
5. Remove `tightenco/ziggy` JS dependency (keep composer package if other things depend on it)

### Inertia v2 → v3 Migration

**Packages:**
- `composer require inertiajs/inertia-laravel:^3.0`
- `npm install @inertiajs/vue3@^3.0`
- Run `php artisan vendor:publish --provider="Inertia\ServiceProvider" --force`
- Run `php artisan view:clear`

**Breaking changes that affect Lua:**

1. **Axios removed from Inertia** — Lua uses axios in 5 files (bootstrap.js, Menu.vue, Logo.vue, Avatar.vue, Qrcode.vue). Options:
   - Keep axios as explicit dependency (it's in package.json already), OR
   - Migrate to Inertia's `useHttp()` hook for those requests
   - Decision: keep axios for now, migrate later

2. **`<title inertia>` → `<title data-inertia>`** in `app.blade.php`

3. **`Inertia::lazy()` → `Inertia::optional()`** — grep shows Lua doesn't use `lazy()`, so no change needed

4. **`router.cancel()` → `router.cancelAll()`** — Lua doesn't use this

5. **Event renames** — `invalid` → `httpException`, `exception` → `networkError` — Lua doesn't use these

6. **`future` options removed** — Lua doesn't set these

7. **Config restructured** — `testing.page_paths` → `pages.paths` etc. Handled by `vendor:publish --force`

8. **`useForm` processing state** — now only resets in `onFinish`. Review forms that depend on `processing` state.

**New config/inertia.php** (copy from SendKit):
```php
'ssr' => [
    'enabled' => (bool) env('INERTIA_SSR_ENABLED', false), // disabled for Lua
],
'pages' => [
    'ensure_pages_exist' => false,
    'paths' => [resource_path('js/pages')], // lowercase after migration
    'extensions' => ['js', 'jsx', 'ts', 'tsx', 'vue'],
],
'testing' => ['ensure_pages_exist' => true],
'expose_shared_prop_keys' => true,
'history' => ['encrypt' => false],
```

**HandleInertiaRequests.php updates:**
- Add `sidebarOpen` prop (from cookie, like SendKit)
- Add `appearance` prop (from cookie, for SSR dark mode)
- Keep workspace-based auth structure (different from SendKit's team-based)

**Shared page props typed in `global.d.ts`:**
```ts
declare module '@inertiajs/core' {
  export interface InertiaConfig {
    sharedPageProps: {
      name: string;
      auth: Auth;
      flash: { banner?: string; bannerStyle?: string };
      sidebarOpen: boolean;
      appearance: string;
      env: string;
      locale: string;
    };
  }
}
```

### Echo Migration

SendKit uses `@laravel/echo-vue` (new Vue-specific Echo package) with `configureEcho()`.
Lua currently uses `laravel-echo` + `pusher-js` with global `window.Echo`.

Migration:
- Install `@laravel/echo-vue`
- Replace `echo.js` with `echo.ts` using `configureEcho()` pattern from SendKit
- Remove `pusher-js` from dependencies (bundled in echo-vue)
- Remove `window.Echo` and `window.Pusher` globals

### Appearance / Dark Mode System

Replace Lua's current `theme.js` (`useDarkTheme` composable) with SendKit's `useAppearance.ts`:
- Supports 3 modes: light, dark, system
- Persists to localStorage + cookie (for SSR)
- `initializeTheme()` called in `app.ts` after mount
- Theme switcher in user dropdown menu (via `UserMenuContent.vue`)
- Blade template has inline script to prevent FOUC

### TypeScript Config

Copy `tsconfig.json` from SendKit verbatim. Key settings:
- `target`: ESNext, `module`: ESNext, `moduleResolution`: bundler
- `strict`: true
- `paths`: `@/*` → `./resources/js/*`
- `types`: `["vite/client"]`
- `jsx`: preserve, `jsxImportSource`: vue
- `allowJs`: true (allows gradual migration if needed)
- `noEmit`: true, `sourceMap`: true
- `include`: `resources/js/**/*.ts`, `resources/js/**/*.d.ts`, `resources/js/**/*.tsx`, `resources/js/**/*.vue`
```

### shadcn Configuration

Copy `components.json` from SendKit and adapt paths:

```json
{
  "$schema": "https://shadcn-vue.com/schema.json",
  "style": "new-york-v4",
  "tailwind": {
    "config": "tailwind.config.js",
    "css": "resources/css/app.css"
  },
  "aliases": {
    "components": "@/components",
    "composables": "@/composables",
    "utils": "@/lib/utils",
    "ui": "@/components/ui",
    "lib": "@/lib"
  }
}
```

## Page-by-Page Migration Notes

Each page needs:
1. `<script setup>` → `<script setup lang="ts">`
2. `defineProps({...})` → `defineProps<{...}>()`
3. Phosphor icons → Tabler icons
4. HeadlessUI components → shadcn equivalents
5. `route()` (Ziggy) → Wayfinder route helpers
6. Custom CSS classes → shadcn component usage

### Component Mapping

| Lua (current) | Target (shadcn) |
|---|---|
| `<Modal>` | `<Dialog>` |
| `<SlideOver>` | `<Sheet>` |
| `<Dropdown>` (HeadlessUI Listbox, plain) | `<Select>` (shadcn) |
| `<Dropdown>` (HeadlessUI Listbox, with search) | `<Combobox>` (shadcn) |
| `<Input>` | `<Input>` (shadcn) |
| `<Label>` | `<Label>` (shadcn) |
| `<Select>` | `<NativeSelect>` or `<Select>` (shadcn) |
| `<Checkbox>` | `<Checkbox>` (shadcn) |
| `<Radio>` | Use shadcn RadioGroup |
| `<Textarea>` | Keep native, style with shadcn classes |
| `<Toggle>` | `<Switch>` (shadcn) |
| `<Tab>` | `<Tabs>` (shadcn) |
| `<Table>` | `<Table>` (shadcn) |
| `<Tag>` | `<Badge>` (shadcn) |
| `<Pagination>` | Rewrite with shadcn `<Button>` |
| `<Banner>` (toast) | `<Sonner>` |
| `<Accordion>` | `<Collapsible>` (shadcn) |
| `<ConfirmDeleteModal>` | `<AlertDialog>` (shadcn) |
| `<DialogModal>` | `<Dialog>` (shadcn) |
| `<ConfirmationModal>` | `<AlertDialog>` (shadcn) |
| `<RangePicker>` | `<DateRangePicker>` (shadcn) |
| `<DatePicker>` | `<DatePicker>` (from SendKit) |
| `<FormSection>` | Remove, use inline layout |
| `<ActionSection>` | Remove, use inline layout |
| `<SectionTitle>` | `<Heading>` (from SendKit) |
| `<SectionBorder>` | `<Separator>` (shadcn) |

### Wayfinder Laravel Setup

Server-side:
1. `composer require laravel/wayfinder`
2. Wayfinder auto-generates TypeScript route helpers from Laravel routes
3. Generated into `resources/js/routes/` (or wherever configured)
4. Each route becomes a typed function: `index()`, `show(id)`, `store()`, etc.
5. Import pattern: `import { index as linksIndex } from '@/routes/app/links';`

### Appearance Middleware

SendKit passes `$appearance` to the blade template for SSR dark mode. Need to add middleware or share this via `HandleInertiaRequests`:

```php
// In HandleInertiaRequests.php share():
'appearance' => $request->cookie('appearance', 'system'),
'sidebarOpen' => $request->cookie('sidebar_state', true),
```

## Testing

- Run `npx vite build` after migration to verify no build errors
- Run `vue-tsc --noEmit` to verify TypeScript types
- Run `php artisan test` to verify backend still works
- Manual testing of all pages in browser
- Verify dark mode works (light/dark/system)
- Verify mobile sidebar works (collapsible)
- Verify toast notifications work (flash messages)
- Verify all forms submit correctly (Inertia v3)

## Files Count Estimate

- ~207 UI component files (copied from SendKit)
- ~15 composable/lib/type files (copied + adapted)
- ~7 layout files (copied + adapted)
- ~99 existing .vue files rewritten (JS → TS, new components)
- ~10 config files (tsconfig, components.json, vite.config.ts, app.blade.php, etc.)
- ~9 existing .js utility files → .ts
- **Total: ~347 files touched**
