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

- **SSR is off.** `config/inertia.php` defaults `INERTIA_SSR_ENABLED` to `false`.
- **The reason is that the marketing site left.** Server rendering was carrying the public pages — a search engine reading `/pricing` or a blog post needs markup in the response, not an empty `#app`. Everything this application still serves sits behind a login, where nothing crawls and nobody shares a link for its preview, so server rendering costs a second process and a whole class of silent failure and buys nothing.
- **The wiring is intact and still correct**, so turning it back on is one environment variable and a running `php artisan inertia:start-ssr`. What follows describes that wiring, and is the reason the switch is cheap. It is also why none of it should be ripped out on the grounds that it looks unused.
- There is **no `ssr.ts`**. Since Inertia 3 the `@inertiajs/vite` plugin builds the server bundle from `resources/js/app.ts`, so there is one entrypoint that cannot drift from the other. `npm run build` runs both passes and writes the server bundle to `bootstrap/ssr/app.js`, which `config/inertia.php` points at.
- **`app.ts` runs in Node as well as the browser** whenever SSR is on. Anything touching `window`, `document`, `localStorage` or a WebSocket has to be behind `typeof window !== 'undefined'` or it throws at import time and every page falls back to client rendering. `resources/js/bootstrap.ts` is the pattern: axios on the window and the Echo connection are both guarded, and Echo is a dynamic import so the client is never loaded server-side. **Keep writing code that way** — the guards cost nothing while SSR is off and are what make turning it on a switch rather than a project.
- `createSSRApp` on both sides, not `createApp`: it is what lets the browser hydrate the markup the server sent instead of throwing it away and rendering again.
- **`app.blade.php` carries no `<title>`.** `@inertiaHead` renders it; a static one comes through as a second tag. The default title comes from the `title` callback in `app.ts`.
- **A crash in the SSR process is silent.** Inertia falls back to client rendering and the page still looks fine, so the only sign is server-rendered markup going missing. That failure mode is why leaving SSR on for an application nobody crawls was a liability rather than a safety net.
- Tests pin `INERTIA_SSR_ENABLED=false` in `phpunit.xml`, which now matches the default rather than overriding it.

## The marketing site lives elsewhere

The public site is a separate Nuxt project (`~/Herd/lua-site`) deployed to
Cloudflare, serving `www.lua.sh`. This app serves the short links and the
signed-in product at `lua.sh`, and a request to the bare domain with no key
redirects to `config('app.website')`.

Two things follow. `CreateLink::reservedKeys()` now derives only from this
app's routes, which is correct — `/pricing` is not served here any more.
And the pricing tiers are duplicated: `shared/plans.ts` in the site
repository is a hand-kept copy of the `plans` table and drifts silently, so
update both when a price changes.

## Design system

**The accent is Firecrawl's Heat, `#FA5D19`.** It was chosen on looks and then made to work; the logo is used in its black and white variants, so it constrains nothing.

### Colour

Tokens live in `resources/css/app.css` on `:root` and `.dark`, and reach Tailwind through `@theme inline`. **Always use the semantic token, never a raw hex.**

| Token | Light | Dark | Notes |
| --- | --- | --- | --- |
| `foreground` | `#000000` | | **Pure black, not near-black.** `full-black.svg` is drawn in `fill="black"`, so `#0a0a0a` type put the wordmark and the nav a shade apart inside the same header. |
| `primary` | `#fa5d19` | `#fa5d19` | One button colour everywhere. **Its label is ink, not white**: white on Heat is 3.16:1 and fails; `#1a0c04` on it is 6.26:1. |
| `primary-text` | `#be3c04` | `#fd9a6b` | **The accent as text**, for links and small marks. A second token is forced, not decorative: Heat is bright enough to be a button against white and therefore too bright to read as text on it (3.16:1). Use `text-primary-text`, never `text-primary`. |
| `destructive` | `#be123c` | `#fb7185` | **Moved off red, and this is load-bearing.** Heat sits at a CIEDE2000 of about 16 from the stock `#dc2626`, and the app confirms deletions in four places, so Save and Delete would read as the same button. Against the crimson it clears 25. Do not put the stock red back. |
| `border` / `input` / `muted` | warm neutrals | warm neutrals | Greys pulled a few degrees toward the accent. |
| `chart-1..5` | deep rust → pale peach | reversed | Reversed per theme so every band stays visible on its ground. |
| `radius` | `0.75rem` | | |

Contrast is computed, not eyeballed. If you add a colour, compute it.

### Typography

| Token | Family | Use |
| --- | --- | --- |
| `font-sans` | Inter | Everything else: body, all app UI, all data |
| `font-mono` | system stack | URLs, keys, code |

**The app keeps Inter everywhere.** A display face in a data table costs legibility and buys nothing.

### Space, radius, depth

- `rounded-md` on controls, `rounded-xl` on cards, `rounded-2xl` on panels, `rounded-full` on avatars and pills.
- Shadows are faint by design (2 to 4% opacity). **Structure comes from borders.** Do not reach for a heavier shadow to create hierarchy.

### Motion

- `prefers-reduced-motion` is honoured everywhere, without exception.
- Hover changes opacity or border, never the accent's hue.
- Nothing animates on load. A page that assembles itself is a page nobody can read while it does.

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

- **The production stage carries `node` at runtime** so `inertia:start-ssr` can execute `bootstrap/ssr/app.js`. SSR is off by default now and the supervisord program does not autostart, so nothing runs it today — but it stays installed, because removing it would turn re-enabling SSR from an environment variable into an image rebuild. When it was needed and missing, the failure was invisible: the container stayed healthy, `/up` answered 200, and every page quietly fell back to client rendering.
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
