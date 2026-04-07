import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \Laravel\Horizon\Http\Controllers\WorkloadController::index
* @see vendor/laravel/horizon/src/Http/Controllers/WorkloadController.php:15
* @route '/horizon/api/workload'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/horizon/api/workload',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Horizon\Http\Controllers\WorkloadController::index
* @see vendor/laravel/horizon/src/Http/Controllers/WorkloadController.php:15
* @route '/horizon/api/workload'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\WorkloadController::index
* @see vendor/laravel/horizon/src/Http/Controllers/WorkloadController.php:15
* @route '/horizon/api/workload'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\WorkloadController::index
* @see vendor/laravel/horizon/src/Http/Controllers/WorkloadController.php:15
* @route '/horizon/api/workload'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Laravel\Horizon\Http\Controllers\WorkloadController::index
* @see vendor/laravel/horizon/src/Http/Controllers/WorkloadController.php:15
* @route '/horizon/api/workload'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\WorkloadController::index
* @see vendor/laravel/horizon/src/Http/Controllers/WorkloadController.php:15
* @route '/horizon/api/workload'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\WorkloadController::index
* @see vendor/laravel/horizon/src/Http/Controllers/WorkloadController.php:15
* @route '/horizon/api/workload'
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

const workload = {
    index: Object.assign(index, index),
}

export default workload