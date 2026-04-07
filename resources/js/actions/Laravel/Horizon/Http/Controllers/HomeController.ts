import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults, validateParameters } from './../../../../../wayfinder'
/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
export const index = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/horizon/{view?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
index.url = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { view: args }
    }

    if (Array.isArray(args)) {
        args = {
            view: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "view",
    ])

    const parsedArgs = {
        view: args?.view,
    }

    return index.definition.url
            .replace('{view?}', parsedArgs.view?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
index.get = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
index.head = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
const indexForm = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
indexForm.get = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\HomeController::index
* @see vendor/laravel/horizon/src/Http/Controllers/HomeController.php:14
* @route '/horizon/{view?}'
*/
indexForm.head = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const HomeController = { index }

export default HomeController