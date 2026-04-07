import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \Laravel\Horizon\Http\Controllers\MasterSupervisorController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MasterSupervisorController.php:18
* @route '/horizon/api/masters'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/horizon/api/masters',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Horizon\Http\Controllers\MasterSupervisorController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MasterSupervisorController.php:18
* @route '/horizon/api/masters'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\MasterSupervisorController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MasterSupervisorController.php:18
* @route '/horizon/api/masters'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MasterSupervisorController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MasterSupervisorController.php:18
* @route '/horizon/api/masters'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MasterSupervisorController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MasterSupervisorController.php:18
* @route '/horizon/api/masters'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MasterSupervisorController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MasterSupervisorController.php:18
* @route '/horizon/api/masters'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MasterSupervisorController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MasterSupervisorController.php:18
* @route '/horizon/api/masters'
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

const masters = {
    index: Object.assign(index, index),
}

export default masters