import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\UsageController::index
* @see app/Http/Controllers/Setting/UsageController.php:11
* @route '/settings/usage'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/usage',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\UsageController::index
* @see app/Http/Controllers/Setting/UsageController.php:11
* @route '/settings/usage'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\UsageController::index
* @see app/Http/Controllers/Setting/UsageController.php:11
* @route '/settings/usage'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\UsageController::index
* @see app/Http/Controllers/Setting/UsageController.php:11
* @route '/settings/usage'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\UsageController::index
* @see app/Http/Controllers/Setting/UsageController.php:11
* @route '/settings/usage'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\UsageController::index
* @see app/Http/Controllers/Setting/UsageController.php:11
* @route '/settings/usage'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\UsageController::index
* @see app/Http/Controllers/Setting/UsageController.php:11
* @route '/settings/usage'
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

const UsageController = { index }

export default UsageController