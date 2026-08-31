import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\AlternativeController::index
* @see app/Http/Controllers/Site/AlternativeController.php:22
* @route '//lua.test/alternatives'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//lua.test/alternatives',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\AlternativeController::index
* @see app/Http/Controllers/Site/AlternativeController.php:22
* @route '//lua.test/alternatives'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\AlternativeController::index
* @see app/Http/Controllers/Site/AlternativeController.php:22
* @route '//lua.test/alternatives'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\AlternativeController::index
* @see app/Http/Controllers/Site/AlternativeController.php:22
* @route '//lua.test/alternatives'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\AlternativeController::index
* @see app/Http/Controllers/Site/AlternativeController.php:22
* @route '//lua.test/alternatives'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\AlternativeController::index
* @see app/Http/Controllers/Site/AlternativeController.php:22
* @route '//lua.test/alternatives'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\AlternativeController::index
* @see app/Http/Controllers/Site/AlternativeController.php:22
* @route '//lua.test/alternatives'
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
* @see \App\Http\Controllers\Site\AlternativeController::show
* @see app/Http/Controllers/Site/AlternativeController.php:39
* @route '//lua.test/alternatives/{slug}'
*/
export const show = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '//lua.test/alternatives/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\AlternativeController::show
* @see app/Http/Controllers/Site/AlternativeController.php:39
* @route '//lua.test/alternatives/{slug}'
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
* @see \App\Http\Controllers\Site\AlternativeController::show
* @see app/Http/Controllers/Site/AlternativeController.php:39
* @route '//lua.test/alternatives/{slug}'
*/
show.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\AlternativeController::show
* @see app/Http/Controllers/Site/AlternativeController.php:39
* @route '//lua.test/alternatives/{slug}'
*/
show.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\AlternativeController::show
* @see app/Http/Controllers/Site/AlternativeController.php:39
* @route '//lua.test/alternatives/{slug}'
*/
const showForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\AlternativeController::show
* @see app/Http/Controllers/Site/AlternativeController.php:39
* @route '//lua.test/alternatives/{slug}'
*/
showForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\AlternativeController::show
* @see app/Http/Controllers/Site/AlternativeController.php:39
* @route '//lua.test/alternatives/{slug}'
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

const alternatives = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
}

export default alternatives