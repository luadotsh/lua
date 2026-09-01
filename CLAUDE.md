<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.5. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project keeps committed, area-grouped rules in `.ai/rules` (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

</laravel-boost-guidelines>

# Project-Specific Rules

## Frontend (Vue/TypeScript)

- Always use arrow functions in Vue components and TypeScript files. Never use `function` declarations.

## Inertia SSR

- **SSR is on**, in development and in production alike, so a bug that only appears server-side is caught while you are working rather than in someone's search results.
- There is **no `ssr.ts`**. Since Inertia 3 the `@inertiajs/vite` plugin builds the server bundle from `resources/js/app.ts`, so there is one entrypoint that cannot drift from the other. `npm run build` runs both passes and writes the server bundle to `bootstrap/ssr/app.js`, which `config/inertia.php` points at.
- `npm run dev` serves SSR itself — no second process. In production run `php artisan inertia:start-ssr` alongside the app.
- **`app.ts` runs in Node as well as the browser.** Anything touching `window`, `document`, `localStorage` or a WebSocket has to be behind `typeof window !== 'undefined'` or it throws at import time and every page falls back to client rendering. `resources/js/bootstrap.ts` is the pattern: axios on the window and the Echo connection are both guarded, and Echo is a dynamic import so the client is never loaded server-side.
- `createSSRApp` on both sides, not `createApp`: it is what lets the browser hydrate the markup the server sent instead of throwing it away and rendering again.
- **`app.blade.php` carries no `<title>`.** `@inertiaHead` renders it; a static one comes through as a second tag. The default title comes from the `title` callback in `app.ts`.
- **The SSR server holds the bundle in memory.** After `npm run build`, restart it (`php artisan inertia:stop-ssr && php artisan inertia:start-ssr`) or you are debugging the previous build. The symptom is markup that looks a version behind while the client-side app is current.
- **A crash in the SSR process is silent.** Inertia falls back to client rendering and the page still looks fine, so the only sign is server-rendered markup going missing. Check the process output. A plugin that reads a browser global at install time is the usual culprit — `laravel-vue-i18n` is passed an explicit `lang` from the shared `locale` prop for exactly this reason, because it otherwise reads `document`.
- Tests pin `INERTIA_SSR_ENABLED=false` in `phpunit.xml`. With it on and nothing listening, Inertia falls back to client rendering silently, so the suite would pass either way — a difference no test would report, and the run would depend on whether a server happened to be up.

## Marketing site

- The public site (`/`, `/pricing`, `/terms`, `/privacy`, `/alternatives`) lives in this app — there is no separate `www` project, and no `app.website` config.
- `routes/site.php` is **scoped to `config('domains.main')`** and required from the **top** of `routes/web.php`. Both halves matter: registration order is what makes `/pricing` win over the `/{key?}` short-link catch-all at the bottom, and the domain scope is what keeps a customer's own domain free to serve `example.com/pricing` as a short link.
- **Reserved back-halves are derived, not listed.** `CreateLink::reservedKeys()` reads the router for every first path segment registered on the main domain, so adding a route reserves it automatically. Never replace it with a hand-kept list. It applies only on the main domain.
- Adding a competitor comparison is **one key in `config/alternatives.php`** — no route, no component, no registry. The config keys *are* the registry and the slug is the URL. `tests/Feature/Site/AlternativeTest.php` asserts the shape every entry needs, because the template reads those keys unconditionally.
- Slugs are constrained to `[a-z0-9-]+` in the route: the controller reads back with `config("alternatives.{$slug}")`, and a dot would traverse into a nested key rather than miss.
- Page titles carry **no brand** — `createInertiaApp`'s `title` callback appends it. `components/site/Seo.vue` adds it back for the OG and Twitter tags, which bypass that callback.

## FAQ

- Questions live in `config/faq.php`, grouped. It is the **one source** for three surfaces: the `/faq` page, the short set on the home page, and the `FAQPage` structured data both emit. Adding a question adds it everywhere.
- `home: true` marks the handful that lead the home page. They are the ones a stranger asks before signing up, not the ones a customer asks after.
- `PageController::faqGroups(homeOnly:)` shapes both. `tests/Feature/Site/FaqTest.php` asserts the home set is a strict subset, because two lists would drift into two different answers to the same question on the same site.
- Every answer must be true of the product as it stands. The list of what Lua does **not** do is in `.claude/blog-context.md`.

## Content-driven pages

Four surfaces share one arrangement, and a fifth should copy it rather than invent another: **the config keys are the registry, the key is the URL slug, and adding an entry touches no route and no component.**

| Surface | Config | Notes |
| --- | --- | --- |
| Alternatives | `config/alternatives.php` | One competitor per key |
| Use cases | `config/use_cases.php` | Every entry needs a `caveat` |
| Glossary | `config/glossary.php` | `related` must resolve; a test fails a dangling slug |
| FAQ | `config/faq.php` | Grouped; `home: true` marks the home-page set |

Slugs are constrained to `[a-z0-9-]+` in the route because the controller reads back with `config("x.{$slug}")`, and a dot would traverse into a nested key rather than miss. Each has a shape test, because the templates read their keys unconditionally: an entry added with a section missing renders broken rather than failing.

## Use cases and tools

- **Use cases** are `config/use_cases.php`, the same arrangement as `config/alternatives.php`: keys are the registry, slug is the URL, adding one touches no route and no component. Every entry needs a `caveat` naming where Lua stops helping, and a test fails an empty one — a page that only sells is a page nobody believes the rest of.
- **Tools** are real and work without an account. The UTM builder and the QR generator run entirely in the browser and send nothing anywhere, which is the claim their pages make and has to stay true.
- **The redirect checker fetches a URL a stranger chose, from our server.** `App\Actions\Tool\FollowRedirects` is written around that: http/https only, the host resolved and **every** address checked against private, loopback, link-local and reserved ranges **on every hop**, redirects followed one at a time so that per-hop check is possible at all, a hop limit, a short timeout, and the response body never read. The route is throttled. `ToolTest` asserts nothing is sent for five separate vectors; do not relax any of it.
- `qrcode` ships no types and `@types/qrcode` would be a new dependency, so the two calls used are declared in `resources/js/types/qrcode.d.ts`. Widen that rather than reaching for `any`.

## Blog

- Posts are markdown files in `resources/blog/<slug>.md`. The directory is the database: dropping a file in publishes a post, and there is no index, registry or seeder to update alongside it.
- Frontmatter: `title`, `description`, `date` (required — a post with no date never publishes), `author`, `image`, `tags`, `draft`. A `date` in the future is a scheduled draft: `ListPosts` keeps it out of the listing **and** 404s its own URL, so writing ahead is safe.
- **symfony/yaml resolves a bare `2026-08-20` into a Unix timestamp.** `ListPosts::date()` normalises it; never read `frontmatter.date` raw.
- Markdown is rendered to HTML **on the server** by `RenderPost` (League\CommonMark, already a Laravel dependency — no parser ships to the browser). The same pass that writes `id` onto each `h2`/`h3` is the one that reports the headings to the table of contents, so the two can never disagree.
- `RenderPost` caches on `path + mtime`, so editing a post invalidates its own entry and nothing has to be cleared.
- Body styling is `prose` from `@tailwindcss/typography`, which is already loaded in `resources/css/app.css`.

## Design system

**The accent is Firecrawl's Heat, `#FA5D19`.** It was chosen on looks and then made to work; the logo is used in its black and white variants, so it constrains nothing.

### Colour

Tokens live in `resources/css/app.css` on `:root`, `.dark` and `.site-light`, and reach Tailwind through `@theme inline`. **Always use the semantic token, never a raw hex.**

| Token | Light | Dark | Notes |
| --- | --- | --- | --- |
| `foreground` | `#000000` | | **Pure black, not near-black.** `full-black.svg` is drawn in `fill="black"`, so `#0a0a0a` type put the wordmark and the nav a shade apart inside the same header. |
| `primary` | `#fa5d19` | `#fa5d19` | One button colour everywhere. **Its label is ink, not white**: white on Heat is 3.16:1 and fails; `#1a0c04` on it is 6.26:1. |
| `primary-text` | `#be3c04` | `#fd9a6b` | **The accent as text**, for links and small marks. A second token is forced, not decorative: Heat is bright enough to be a button against white and therefore too bright to read as text on it (3.16:1). Use `text-primary-text`, never `text-primary`. |
| `destructive` | `#be123c` | `#fb7185` | **Moved off red, and this is load-bearing.** Heat sits at a CIEDE2000 of about 16 from the stock `#dc2626`, and the app confirms deletions in four places, so Save and Delete would read as the same button. Against the crimson it clears 25. `DesignSystemTest` asserts both directions, so putting the stock red back fails with the reason attached. |
| `border` / `input` / `muted` | warm neutrals | warm neutrals | Greys pulled a few degrees toward the accent. |
| `chart-1..5` | deep rust → pale peach | reversed | Reversed per theme so every band stays visible on its ground. |
| `radius` | `0.75rem` | | Cards go further, at `1.25rem` via `.site-card`. |

Contrast is computed, not eyeballed. If you add a colour, compute it.

### The marketing site is light. Always.

`.site-light` on the `SiteLayout` root redefines the whole token set, so the public pages render light whatever `.dark` is doing on `<html>`. **The signed-in app keeps its dark mode**; only the site is fixed.

This is a decision, not an omission. A marketing page has one job and a reader passes through it once, so a second palette doubles the design work and halves the attention each version gets. It is a token scope rather than a class stripped off `<html>`, because that class belongs to the app and navigating between the two must not fight over it.

`DesignSystemTest` fails on any `dark:` variant inside `pages/Site`, `components/site` or `layouts/site` — a dark variant there is a second palette nobody is designing, visible only to a reader whose app theme happens to be dark.

**`.brand-panel` is the one dark surface**: the footer, anchoring the bottom of a white page on **pure `#000`**, matching the black the type and the wordmark use. Like `.site-light` it is a token scope, so an unmodified `<Button>` or `<Link>` works inside it. Writing hexes onto components instead is exactly how the header's "Start for free" once drifted from every other primary button, and the test also fails on any `bg-[#...]`-style colour in those directories. The single exception is `HeroGlobe.vue`, where mapbox paints through WebGL and cannot resolve a custom property.

### The structural device: a ruled column

`SiteLayout` wraps every page in `.site-rail` — two hairlines at the container edges running the full height, which each section's bottom rule then crosses. It reads as a drawing rather than as a stack of blocks, which is the register a measurement product wants.

**Pages therefore carry no container of their own.** A section is `border-b border-border px-6 py-16 sm:px-10 sm:py-24` and nothing more; adding `mx-auto max-w-6xl` back inside puts a second column inside the first and breaks the rails.

Supporting devices, all of them once each:

- `.site-grid` — a faint dot grid, masked, behind the hero only. Drawing sheet, not wallpaper.
- `.label` — small mono, uppercase, tracked. **This is the eyebrow register**, and it is the reason eyebrows no longer need the accent to be noticed. Three type registers carry the site: display for headlines, mono for labels, Inter for everything else.
- Ghosted display numerals (`text-border`, `text-5xl`) on the steps, like dimensions on a drawing.
- `bg-muted/40` on alternating sections, so a long page has rhythm the rules alone cannot give it.
- `.site-card` for anything that should read as an object: hairline border plus a soft shadow. Border-only at a tight radius is what made earlier passes look flat and drawn.

### Where the accent goes

`--primary` is for **buttons and small marks**, not for running text. Section eyebrows use the `.label` register instead: an accent on every eyebrow down a long page made the palette read as the loudest thing on the site rather than the sharpest.

`brand-gradient-text` is the headline treatment and belongs to the home page's `h1` alone; a test enforces that. It has a `forced-colors` fallback, because that mode strips the background and would otherwise leave the headline blank.

### Typography

| Token | Family | Use |
| --- | --- | --- |
| `font-display` | Schibsted Grotesk 700, `-0.032em` | Marketing headlines only |
| `font-sans` | Inter | Everything else: body, all app UI, all data |
| `font-mono` | system stack | URLs, keys, code |

Set heavy and tight on purpose. With the palette restrained to a single accent, the headline carries the page, and a grotesque at 700 with negative tracking is what makes a short line read as a statement rather than a label. A high-contrast serif was tried here first and read as fashion editorial rather than as a technical product.

**The app keeps Inter everywhere.** A display face in a data table costs legibility and buys nothing. Apply `font-display` to `h1`/`h2` at `text-3xl` and above on `pages/Site/**` and `components/site/**`, nowhere else; a test enforces it.

### Space, radius, depth

- Section rhythm: `py-16 sm:py-24` standard, `py-20 sm:py-28` for the hero.
- Container: `mx-auto w-full max-w-6xl px-4 sm:px-6`. Articles narrow to `max-w-3xl` for line length.
- Sections are separated by `border-b border-border`, never by shadow or a background change.
- `rounded-md` on controls, `rounded-xl` on cards and mockups, `rounded-2xl` on panels, `rounded-full` on avatars and pills.
- Shadows are faint by design (2 to 4% opacity). **Structure comes from borders.** Do not reach for a heavier shadow to create hierarchy.

### Section pattern

```html
<p class="text-sm font-medium text-primary">Eyebrow</p>
<h2 class="mt-2 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
    Headline
</h2>
<p class="mt-4 text-lg text-muted-foreground">Standfirst</p>
```

The eyebrow is the only place the accent appears as text. It names the section in one word so the page can be skimmed down its left edge.

### Motion

- `prefers-reduced-motion` is honoured everywhere, without exception.
- Hover changes opacity or border, never the accent's hue.
- Nothing animates on load. A page that assembles itself is a page nobody can read while it does.

### Adding to this

Tokens are shared by the marketing site and the signed-in app: one brand, one document, no second set of rules to drift. A change to `--primary` restyles both, which is the intent, so check both before shipping one.

## The hero

The hero is the drawing sheet the rest of the page is ruled to, so it is the one section that uses every device at once.

- The headline sits **in** the column, left, at `text-7xl` with tight tracking. Centred text floating in the middle of the rails was the weak version: it ignored the one structural idea the page has.
- A **title block** takes the right margin, divided by a hairline, in the `.label` register with mono values. It states facts rather than adjectives, and **every line has to stay checkable** — that is the only reason it carries.
- **The title block does not mention the licence.** Open source is a real differentiator and it appears in three other places on the page, but leading the hero with a licence answers a question almost nobody arrives with. The block states what the product does: analytics on every plan, history kept, a domain live in minutes, the three interfaces.
- The product surface follows, **cropped by the section's own rule**, so the screen continues under it rather than ending at a hard stop. Crop on a clean line: through the chart, not through the first row of a list, or it reads as clipped rather than as deliberate.
- The three pillar cards moved below the fold onto the alternating ground. They are elaboration; the headline and the product are the argument.

A globe sat beside the headline first and it was wrong at every size: a pale ball with country labels for noise, leaving a large empty block under the buttons. Shrinking it into a dark card did not save it either. **A hero should show the thing being sold**, and here that is the analytics, so the mockup carries it and the globe is gone.

## Marketing visuals

- Product imagery is **drawn in markup**, never screenshotted. `components/site/AnalyticsMockup.vue` and `ShortenMockup.vue` restyle with the theme, stay sharp on any display, use the same country and browser icons the real screens do, and cannot go stale against a screenshot nobody retook.
- Illustrative numbers stay unremarkable and **fixed**, never randomised — a random series is a hydration mismatch, and inflated numbers are an invented case study.
- **The site has no globe, deliberately.** One was built, moved, resized and eventually cut. It broke silently three times (any mutation of a v3 mapbox style layer stops tile loading with no error), and at every size it read as a pale ball rather than as information. The country breakdown in `AnalyticsMockup.vue` says the same thing better. `VisitorsMap.vue` in the app is unaffected and still the right tool there, where the data is real.

## Anchor links and Inertia history

- Anchor navigation is **native**: a plain `<a href="#id">`, `scroll-behavior: smooth` on `html` (with an `auto` override under `prefers-reduced-motion`), and `scroll-mt` on the headings for the sticky header. Do not script it.
- **Never call `history.replaceState(null, ...)`.** Inertia keeps its page object in `history.state`; replacing it with `null` strands the router and the next interaction navigates to the wrong page. This actually happened in the blog TOC. If you must touch history, pass `history.state` back.
- Scripting a scroll and then writing the hash does not work either: Inertia rewrites history whenever it saves a scroll position, so a hash set from JavaScript is wiped moments later and the section becomes unlinkable.

## Dialogs

- In `<DialogFooter>`, put the **primary action button first** in the markup, then secondary/cancel (e.g. Save → Cancel). `DialogFooter` uses `flex-col` on mobile (primary on top, cancel at the bottom) and `sm:flex-row sm:justify-start` on desktop, so the first child is the leftmost action on larger screens.
- Match sibling dialogs in the same feature area before inventing a new footer layout.

## Icons (@tabler/icons-vue)

- This project uses `@tabler/icons-vue` for all icons. NEVER use `lucide-vue-next`.
- All Tabler icons are prefixed with `Icon`, e.g. `IconCheck`, `IconChevronRight`, `IconMail`.
- Import icons from `@tabler/icons-vue`: `import { IconCheck, IconX } from '@tabler/icons-vue'`.
- Browse available icons at https://tabler.io/icons

## Dates

- For date manipulation, always use `@/dayjs` (pre-configured dayjs instance with utc, timezone, relativeTime plugins).
- For formatting dates for display (formatDate, formatDateTime, formatTime, diffForHumans), always use `@/date` which centralizes all formatting logic with proper timezone handling.
- Never use raw `new Date()` for date calculations — use dayjs.

## Routing (Wayfinder)

- This project uses Laravel Wayfinder for type-safe frontend routing.
- ALWAYS use Wayfinder-generated route helpers in Vue pages (e.g. `register()`, `login()`, `dashboard()`). NEVER hardcode URL strings like `href="/register"`.
- After creating or modifying PHP routes/controllers, run `php artisan wayfinder:generate` to regenerate the TypeScript route helpers.
- Import routes from `@/routes/...` (e.g. `import { store } from '@/routes/login'`).
- **The generated output is not in the repository.** `resources/js/actions`, `resources/js/routes` and `resources/js/wayfinder` are gitignored, as Wayfinder's own README instructs — the Vite plugin rebuilds them on every `npm run build`, and tracking them meant a route change churning dozens of TS files in every diff. They are excluded from Prettier, ESLint and Docker for the same reason: the generator and the formatters otherwise undo each other forever. A fresh clone has none of them until it builds once.
- **A route pointing at a method that does not exist registers and boots fine**, and only fails as a 500 when someone requests it. `tests/Feature/RouteTargetTest.php` asserts every `App\` route resolves to a real action, because that is exactly how `setting.billing.swap-free-plan` stayed broken unnoticed.

## Pagination

- Always use normal pagination (`->paginate()`). NEVER use cursor pagination (`->cursorPaginate()`).
- All paginated lists must use Inertia's scroll pagination (`Inertia::scroll()` on the backend with `<InfiniteScroll>` on the frontend). NEVER use traditional page-based pagination with page links/buttons.
- The page size ALWAYS comes from `config('lua.pagination.default')` — never a magic number, and never a `perPage`/`per_page` value supplied by the request or frontend. Action/service list methods must NOT accept a `$perPage` parameter; call `->paginate((int) config('lua.pagination.default'))` directly.
    - The only exception is the public REST API (`app/Http/Controllers/Api`), which uses its own fixed, documented page size (15) as a stable API contract.

## Application Config

- Anything that is a decision about how **this application** behaves — rather than about a framework or a package — lives in `config/lua.php`, not scattered into `config/app.php` or a file of its own. Read it with `config('lua.*')`.
- Every key takes an `env()` with a default that is right for a fresh install, so cloning the repo and running it needs no `.env` edits to work.
- Social login is gated there: a provider appears on login and register only when `lua.auth.<provider>` is on **and** its credentials are set in `config/services.php`. The credential half is what stops a self-hosted install rendering a button that leads straight to an OAuth error; the switch half is what lets you turn a provider off without deleting the credentials to do it. See `App\Enums\Auth\SocialAuthProvider::isEnabled()`.

## Form Validation

- NEVER use HTML5 validation attributes (`required`, `minlength`, `pattern`, etc.) on form inputs. Always rely solely on backend validation.

## Backend Validation

- Validation rules always live in a dedicated `Illuminate\Foundation\Http\FormRequest` subclass under `app/Http/Requests/App/<Group>/`. Controller actions must type-hint the FormRequest as the parameter — NEVER call `$request->validate([...])` inline in the controller.
- Naming: `<Verb><Resource>Request.php` (e.g. `StorePostRequest`, `UpdatePostRequest`, `LinkPreviewRequest`).

## Database engines (PostgreSQL + MySQL)

Lua runs on **both PostgreSQL and MySQL**. Cloud runs PostgreSQL; a self-hosted install may pick either. Every query, migration, and test must work on both — the suite is expected to be green on each.

- **What the app supports is the intersection of the two engines, never the superset of one.** When they differ, take the narrower behaviour — a feature that only holds on PostgreSQL is a feature Lua does not have.
- Never use an engine-specific operator or function. Search uses `whereLike()` (Laravel handles the case-insensitive form per driver), never `ilike` or a raw `LOWER(...)` comparison.
- Traps that only surface on MySQL:
    - **JSON object key order is not preserved.** MySQL reorders object keys on storage (by length, then lexicographically); PostgreSQL keeps insertion order. Assert JSON read back from the database with `toEqual` (recursive, order-independent), never `toBe`/`assertSame`. Array *element* order is preserved on both.
    - **`$table->timestamp()` tops out at 2038-01-19.** PostgreSQL has no such limit, so 2038-01-19 is the app's ceiling: nothing written to a `timestamp()` column may go past it — scheduled posts, expiry sentinels and test fixtures alike. `2037-12-31` reads as "far future" and works on both. Do not widen a column to escape the limit without a deliberate decision; it changes what self-hosted MySQL installs can store.
    - **Raw query-builder reads carry no Eloquent cast**, so the driver's native shape leaks through: `DB::table(...)->value('some_bool')` is `true` on PostgreSQL and `1` on MySQL. Read through the model, or use `assertDatabaseHas`.
    - **Identifier quoting differs** — PostgreSQL emits `"post_platforms"`, MySQL emits backticks. Never match logged SQL (`DB::listen`) against a quoted identifier.
    - **MySQL refuses to drop the only index backing a foreign key** (SQLSTATE `1553`). A migration `down()` that drops a unique whose leftmost prefix is an FK column must create a standalone index for that column first.
    - **DDL implicitly commits**, which defeats `RefreshDatabase`'s rollback: schema changes made inside a test leak into the tests that follow. Keep them idempotent.
- **Aggregates and date maths are where this bites hardest.** `count(*) filter (where ...)` is PostgreSQL-only — write `count(case when ... then 1 end)`, which both engines accept. `date_trunc` and `AT TIME ZONE` are PostgreSQL-only too, and MySQL's `DATE_FORMAT`/`CONVERT_TZ` are the other half of the same problem: neither is portable, so an expression that needs them lives behind a `match (DB::connection()->getDriverName())` and throws on a driver it has no expression for. See `GetTimeseries::bucketExpression()`. **A MySQL server with empty timezone tables silently zeroes every analytics chart.** `CONVERT_TZ` returns `NULL` — not an error — for a *named* zone until `mysql_tzinfo_to_sql` has been loaded, so the bucket is null, no row matches, and the series sums to zero. `GetTimeseries` is already correct; the server is what is missing. The CI loads the tables in the MySQL leg, and `docker/.env.docker.example` says so for self-hosters. Verify with `SELECT COUNT(*) FROM mysql.time_zone_name;` — it must be greater than zero.
- Group by the **output alias**, not by a repeat of the raw expression: both engines accept `group by bucket`, and repeating a parameterised expression makes PostgreSQL treat the two as different and reject the query.

## CI

Three workflows, and the arrangement matters more than it looks.

- `tests.yml` runs the suite as a **matrix over PostgreSQL and MySQL**, with `DB_CONNECTION` coming from the matrix. That last part is load-bearing: `phpunit.xml` pins `pgsql`, so a job that starts only MySQL and never sets `DB_CONNECTION` has the Postgres driver talking to the MySQL port. That was the state for a long time — **510 of 535 tests failed on every PR** and merges over red became normal.
- The **`backend` job is the gate**: it fails unless every matrix leg passed, and it is the check to mark required on `main`. Marking it is a repository setting, not something a PR can do.
- `e2e` runs the browser suite on its own. It has to **serve the app as `lua.test`**, because `routes/site.php` is scoped with `Route::domain(config('domains.main'))` and `route()` therefore emits absolute URLs on that host, ignoring whatever ephemeral port pest-plugin-browser binds. Locally Herd answers there, which means **the marketing browser tests exercise the development app, not the test instance**. A runner has no such host, so the job starts one — with its own database, since `RefreshDatabase` owns `lua_test` and would truncate seeded rows mid-run, and `/pricing` reads real rows from `plans`.
- That server also runs `inertia:start-ssr` and overrides `SESSION_DRIVER`/`CACHE_STORE` back to `file`. The job env sets them to `array` for the test process, which is right there and wrong for a long-lived server: an array session lasts one request, so the CSRF token rendered into the page is gone by the time a form posts and every submission comes back 419.
- **`phpunit.xml` pins `APP_URL`, `DOMAIN_MAIN` and `DOMAIN_CNAME`** next to `DB_CONNECTION`. Link fixtures hardcode `lua.test`, and a local `.env` carries `DOMAIN_MAIN=lua.test` that CI did not have — so the suite passed on every developer machine and failed in CI. A real environment variable still beats phpunit's `<env>`, so `.env.ci` matches.
- `lint.yml` runs Pint, `format:check` and `eslint` in **check mode, never write mode**. A job that repairs files it never commits reports success on a dirty tree — and `eslint --fix` exits 0 after fixing, which hid every auto-fixable violation among 101 error-level rules.

## Lint and formatting

`npm run lint` reports; `npm run lint:fix` repairs. Keep that split — the CI gate uses the first.

Three separate times, a formatting problem turned out to be **two tools owning the same thing** and undoing each other, with `format:check` failing on whichever ran last:

- `import/order` versus `prettier-plugin-organize-imports` → ordering belongs to the formatter, the ESLint rule is off, and `eslint-plugin-import` was removed since nothing else used it.
- Prettier versus Wayfinder → the generated directories are ignored by both.
- `eslint --fix` versus the gate itself → see above.

If a formatting fight reappears, look for the second owner before adjusting either tool.

`vue/no-mutating-props` is **off deliberately**. `LinkForm.vue` takes an Inertia `useForm` as a prop and `v-model`s onto it in nine places; it works, but it is what the rule exists to catch. Moving it to `defineModel` is a refactor of the main form and should be its own change — the `off` is a stay of execution, not approval.

## Docker and releases

`docker/` builds `dev` and `production` targets; a `v*.*.*` tag triggers `release-docker.yml`, which builds amd64 and arm64 on native runners and pushes one multi-arch manifest to GHCR. `/release` (`.claude/commands/release.md`) cuts the tag.

Things that are easy to break here, all of which were:

- **The production stage needs `node` at runtime**, not just at build time — supervisord runs `inertia:start-ssr`, which executes `bootstrap/ssr/app.js` with node. Without it the process exits instantly, supervisord gives up, and nothing shows it: the container is healthy, `/up` answers 200, and every page quietly falls back to client rendering.
- **Do not add `opcache` to `docker-php-ext-install`.** It is already compiled into `php:8.5-fpm-alpine`, and asking for it again fails the build with `cp: can't stat 'modules/*'`. It only needs configuring, which `php.prod.ini` does.
- The entrypoint **refuses to start in production without `PASSPORT_PRIVATE_KEY` and `PASSPORT_PUBLIC_KEY`**, because `storage/` is not a persisted volume and regenerating them on each boot would invalidate every API token.
- The image is large (~2.2GB) because the production stage carries `node_modules`. They cannot simply be dropped: the SSR bundle imports `vue` at runtime and `vue` sits in `devDependencies`.

## Reverb

Broadcasting is wired and **nothing publishes yet** — no events, no `ShouldBroadcast`, an empty `routes/channels.php`, and `BROADCAST_CONNECTION=log`. This is deliberate: real-time work is planned, and the plumbing stays. Do not remove `laravel/reverb`, `laravel-echo` or `@laravel/echo-vue` on the grounds that they look unused.

## Timezones

The analytics timezone comes from the browser and is handed to the database as the argument to `at time zone` / `CONVERT_TZ`, so it is untrusted input that reaches SQL.

- Validate it with **`timezone:all_with_bc`**, never the plain `timezone` rule and never nothing at all. Browsers still report deprecated IANA aliases — Indian clients send `Asia/Calcutta`, not `Asia/Kolkata` — and the plain rule rejects them, which breaks those users outright. Unvalidated is worse: an unknown zone raises an SQL error on PostgreSQL and returns `NULL` on MySQL, which zeroes the chart with no error anywhere.
- `AnalyticsTest` covers both directions, so neither mistake can come back quietly.
- **PHP resolves those aliases from its own bundled database**, not the system's: the image reports `Timezone Database => internal` and works with `/usr/share/zoneinfo` deleted. This is a property of the Alpine base — Debian builds PHP `--with-system-tzdata`, where the deprecated names live in a separate `tzdata-legacy` package. Changing the image's base means installing that package.

## Pest / Feature Tests

- ALWAYS use named routes via the `route()` helper in feature tests. NEVER hardcode URL strings like `'/links/store'`.
    - Example: `$this->postJson(route('links.store'))` instead of `$this->postJson('/links')`.
    - With params: `route('links.store', $creationId)`.

## Browser Tests (Pest + Playwright)

Browser tests live in `tests/Browser` and run on `pestphp/pest-plugin-browser` driving Playwright. **Laravel Dusk is not installed** — there is no `DuskTestCase`, no `$browser` object, and no `browse()`. Do not add `dusk="..."` attributes; they select nothing.

- ALWAYS use named routes via `route()`. NEVER hardcode URLs like `'https://lua.test/login'`.
- **`visit()` returns a *pending* page, and every call made on it materialises a fresh one.** Splitting a chain into separate statements (`$page = visit(...); $page->click(...); $page->script(...)`) silently reloads between steps, so state set in one call is gone by the next and the assertion measures a page that never saw the click. Keep everything an assertion depends on in a single chain.
- **After a click, assert with something that retries.** `assertSee` and `assertDontSee` wait for the text; `assertScript` evaluates once, immediately, and races the re-render the click triggered. The same applies to reading the database after a click that starts an Inertia request: wait for a visible outcome first. Two intermittent failures in this suite were this exact bug, both looking like product flakiness and neither being it.
- **A click on a server-rendered page can land before hydration.** The markup is there, so Playwright happily clicks it, but Vue has not attached a listener yet: the click does nothing and the assertion after it times out waiting for a change nothing triggered. This is not the same as the re-render race above — the click itself is lost. It only shows up where the app is served with SSR, which is why it appeared in CI and not locally.
- **A headless browser is a backgrounded one, and Chrome does not animate a smooth scroll in a backgrounded tab** — the position simply never changes. To assert where a scroll lands, first set `document.documentElement.style.scrollBehavior = 'auto'`; that measures the same jump and is the path a `prefers-reduced-motion` reader gets anyway.
    - Example: `visit(route('login'))`.
- ALWAYS target elements by `data-testid`. NEVER use CSS classes (`.text-red-600`), tag names, or text strings.
    - `@my-element` resolves to `[data-testid="my-element"]`, so add `data-testid="my-element"` in the Vue component and use `$page->click('@my-element')`.
    - Bind it for repeated elements: `:data-testid="`connect-${platform.value}`"`.
- `click()`, `type()` and friends go through Playwright locators, which wait for the target to become actionable — a drill-down of clicks needs no manual waiting between steps. The waits available are `wait()`, `waitForText()`, `waitForKey()` and `waitForEvent()`. `waitForFunction()` exists on `Playwright\Page` but is **not surfaced on the `Webpage` API the tests use**, so there is no predicate wait — where hydration has to be waited for, `wait()` is the honest tool and should say so in a comment.
- `BrowserTestCase` sets `$fakesVite = false` on purpose: these tests load real built assets, so faking Vite blanks the app. Run `npm run build` before running them locally after a frontend change.
- End page assertions with `->assertNoJavaScriptErrors()`.
- `tests/Browser` is NOT a `phpunit.xml` testsuite, so `php artisan test` and `vendor/bin/pest` skip it. Run it explicitly: `php artisan test tests/Browser --compact`. CI does the same, in its own step after `npm run build`.
- Failure screenshots land in `tests/Browser/Screenshots` (gitignored) and are uploaded as a CI artifact.

## Array Data Access

- In Action classes and similar service classes, ALWAYS use Laravel's `data_get()` helper instead of direct array access.
    - Example: `data_get($data, 'name')` instead of `$data['name']`.
    - Use the third parameter for fallback values: `data_get($data, 'username', $sender->username)` instead of `$data['username'] ?? $sender->username`.

## Eloquent Models & Morph Map

- EVERY Eloquent model in `app/Models` MUST be registered in `Relation::enforceMorphMap([...])` inside `AppServiceProvider::configureMorphMap()`, keyed by a camelCase alias (e.g. `'postPlatform' => PostPlatform::class`).
- When you add a new model, add it to the morph map in the same change. `tests/Unit/MorphMapTest.php` fails if any model is missing.
- The alias is persisted in polymorphic columns, so never rename or remove an existing alias for a model that has stored rows.

## Imports

- NEVER use inline class references (e.g., `\DB::listen`, `\Str::uuid()`). ALWAYS import classes at the top of the file with a `use` statement.
    - PHP: `use Illuminate\Support\Facades\DB;` then `DB::listen(...)`
    - TypeScript/Vue: `import { ref } from 'vue'` then `ref(...)`

## API Response Status Codes

- When returning JSON responses with explicit status codes, always use `Symfony\Component\HttpFoundation\Response` constants instead of magic numbers.
    - Example: `Response::HTTP_CREATED` instead of `201`, `Response::HTTP_NO_CONTENT` instead of `204`.

## String Interpolation

- When injecting variables into strings, prefer **double-quoted interpolation** with curly braces over concatenation with `.`.
    - PHP: `"workspace.{$workspace->id}"` instead of `'workspace.'.$workspace->id`.
    - Use curly braces `{}` even for simple variables to keep the boundary explicit and to allow object/array access without ambiguity.
    - Single quotes are still preferred when the string has no interpolation.

## Git

- There are **no git hooks**. Husky and commitlint were removed, so nothing validates a commit message locally — the `chore:` / `fix:` / `feat:` convention is a habit now, not an enforced rule.
- NEVER add `Co-Authored-By` lines to commit messages.
- NEVER commit, push, or open PRs unless explicitly asked by the user.
- Always create a new branch for feature work before making changes.
