import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\UseCaseController::index
* @see app/Http/Controllers/Site/UseCaseController.php:19
* @route '//lua.test/use-cases'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//lua.test/use-cases',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\UseCaseController::index
* @see app/Http/Controllers/Site/UseCaseController.php:19
* @route '//lua.test/use-cases'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\UseCaseController::index
* @see app/Http/Controllers/Site/UseCaseController.php:19
* @route '//lua.test/use-cases'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\UseCaseController::index
* @see app/Http/Controllers/Site/UseCaseController.php:19
* @route '//lua.test/use-cases'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\UseCaseController::index
* @see app/Http/Controllers/Site/UseCaseController.php:19
* @route '//lua.test/use-cases'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\UseCaseController::index
* @see app/Http/Controllers/Site/UseCaseController.php:19
* @route '//lua.test/use-cases'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\UseCaseController::index
* @see app/Http/Controllers/Site/UseCaseController.php:19
* @route '//lua.test/use-cases'
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
* @see \App\Http\Controllers\Site\UseCaseController::show
* @see app/Http/Controllers/Site/UseCaseController.php:36
* @route '//lua.test/use-cases/{slug}'
*/
export const show = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '//lua.test/use-cases/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\UseCaseController::show
* @see app/Http/Controllers/Site/UseCaseController.php:36
* @route '//lua.test/use-cases/{slug}'
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
* @see \App\Http\Controllers\Site\UseCaseController::show
* @see app/Http/Controllers/Site/UseCaseController.php:36
* @route '//lua.test/use-cases/{slug}'
*/
show.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\UseCaseController::show
* @see app/Http/Controllers/Site/UseCaseController.php:36
* @route '//lua.test/use-cases/{slug}'
*/
show.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\UseCaseController::show
* @see app/Http/Controllers/Site/UseCaseController.php:36
* @route '//lua.test/use-cases/{slug}'
*/
const showForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\UseCaseController::show
* @see app/Http/Controllers/Site/UseCaseController.php:36
* @route '//lua.test/use-cases/{slug}'
*/
showForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\UseCaseController::show
* @see app/Http/Controllers/Site/UseCaseController.php:36
* @route '//lua.test/use-cases/{slug}'
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

const useCases = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
}

export default useCases