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
10. **CSS**: Keep Tailwind v4, adopt SendKit's design token system (CSS variables)
11. **Vite**: Update `vite.config.js` → `vite.config.ts` with Wayfinder plugin

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
- tightenco/ziggy (JS package)
- Custom CSS component classes in app.css (.btn, .btn-primary, .card, .badge, .form-input, .table-*, etc.)

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

### Remove

```
@headlessui/vue
@phosphor-icons/vue
floating-vue
tightenco/ziggy (npm — keep composer package if needed for server-side)
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

### CSS Strategy

Replace current custom CSS classes in `app.css` with SendKit's design token approach:

- Copy CSS custom properties (colors, radii, shadows) from SendKit's `app.css`
- Adapt color palette to Lua's brand (violet/purple accent instead of neutral)
- Remove all custom `.btn-*`, `.card`, `.badge-*`, `.form-input`, `.table-*` classes
- Keep Lua-specific styles: `.prose-formatter`, tiptap styles, vue3-colorpicker overrides

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

Key changes:
- Import path stays `@inertiajs/vue3`
- `usePage()` API may have changed
- Check for breaking changes in form helper
- Update `createInertiaApp` setup

### TypeScript Config

Copy from SendKit and adapt:

```json
{
  "compilerOptions": {
    "target": "ESNext",
    "module": "ESNext",
    "moduleResolution": "bundler",
    "strict": true,
    "jsx": "preserve",
    "sourceMap": true,
    "resolveJsonModule": true,
    "esModuleInterop": true,
    "paths": {
      "@/*": ["./resources/js/*"]
    },
    "types": ["vite/client", "node"]
  },
  "include": ["resources/js/**/*.ts", "resources/js/**/*.vue"],
  "exclude": ["node_modules"]
}
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
| `<Dropdown>` (HeadlessUI Listbox) | `<Combobox>` or `<Select>` |
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

## Testing

- Run `npx vite build` after migration to verify no build errors
- Run `php artisan test` to verify backend still works
- Manual testing of all pages in browser
- Verify dark mode works on all pages
- Verify mobile sidebar works

## Files Count Estimate

- ~207 UI component files (copied from SendKit)
- ~10 composable/lib/type files (copied + adapted)
- ~7 layout files (copied + adapted)
- ~99 existing .vue files rewritten (JS → TS, new components)
- ~10 config files (tsconfig, components.json, vite.config.ts, etc.)
- **Total: ~333 files touched**
