import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:47
* @route '/horizon/api/monitoring'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/horizon/api/monitoring',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:47
* @route '/horizon/api/monitoring'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:47
* @route '/horizon/api/monitoring'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:47
* @route '/horizon/api/monitoring'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:47
* @route '/horizon/api/monitoring'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:47
* @route '/horizon/api/monitoring'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::index
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:47
* @route '/horizon/api/monitoring'
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
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::store
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:104
* @route '/horizon/api/monitoring'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/horizon/api/monitoring',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::store
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:104
* @route '/horizon/api/monitoring'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::store
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:104
* @route '/horizon/api/monitoring'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::store
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:104
* @route '/horizon/api/monitoring'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \Laravel\Horizon\Http\Controllers\MonitoringController::store
* @see vendor/laravel/horizon/src/Http/Controllers/MonitoringController.php:104
* @route '/horizon/api/monitoring'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const monitoring = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
}

export default monitoring