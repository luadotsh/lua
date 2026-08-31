import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\GlossaryController::index
* @see app/Http/Controllers/Site/GlossaryController.php:19
* @route '//lua.test/glossary'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//lua.test/glossary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\GlossaryController::index
* @see app/Http/Controllers/Site/GlossaryController.php:19
* @route '//lua.test/glossary'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\GlossaryController::index
* @see app/Http/Controllers/Site/GlossaryController.php:19
* @route '//lua.test/glossary'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\GlossaryController::index
* @see app/Http/Controllers/Site/GlossaryController.php:19
* @route '//lua.test/glossary'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\GlossaryController::index
* @see app/Http/Controllers/Site/GlossaryController.php:19
* @route '//lua.test/glossary'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\GlossaryController::index
* @see app/Http/Controllers/Site/GlossaryController.php:19
* @route '//lua.test/glossary'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\GlossaryController::index
* @see app/Http/Controllers/Site/GlossaryController.php:19
* @route '//lua.test/glossary'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Site\GlossaryController::show
* @see app/Http/Controllers/Site/GlossaryController.php:44
* @route '//lua.test/glossary/{slug}'
*/
export const show = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '//lua.test/glossary/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\GlossaryController::show
* @see app/Http/Controllers/Site/GlossaryController.php:44
* @route '//lua.test/glossary/{slug}'
*/
show.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { slug: args }
    }

    if (Array.isArray(args)) {
        args = {
            slug: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        slug: args.slug,
    }

    return show.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\GlossaryController::show
* @see app/Http/Controllers/Site/GlossaryController.php:44
* @route '//lua.test/glossary/{slug}'
*/
show.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\GlossaryController::show
* @see app/Http/Controllers/Site/GlossaryController.php:44
* @route '//lua.test/glossary/{slug}'
*/
show.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\GlossaryController::show
* @see app/Http/Controllers/Site/GlossaryController.php:44
* @route '//lua.test/glossary/{slug}'
*/
const showForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\GlossaryController::show
* @see app/Http/Controllers/Site/GlossaryController.php:44
* @route '//lua.test/glossary/{slug}'
*/
showForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\GlossaryController::show
* @see app/Http/Controllers/Site/GlossaryController.php:44
* @route '//lua.test/glossary/{slug}'
*/
showForm.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const GlossaryController = { index, show }

export default GlossaryController