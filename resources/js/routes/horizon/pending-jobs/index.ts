import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \Laravel\Horizon\Http\Controllers\PendingJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/PendingJobsController.php:36
* @route '/horizon/api/jobs/pending'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/horizon/api/jobs/pending',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Horizon\Http\Controllers\PendingJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/PendingJobsController.php:36
* @route '/horizon/api/jobs/pending'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\PendingJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/PendingJobsController.php:36
* @route '/horizon/api/jobs/pending'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\PendingJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/PendingJobsController.php:36
* @route '/horizon/api/jobs/pending'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Laravel\Horizon\Http\Controllers\PendingJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/PendingJobsController.php:36
* @route '/horizon/api/jobs/pending'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\PendingJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/PendingJobsController.php:36
* @route '/horizon/api/jobs/pending'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\PendingJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/PendingJobsController.php:36
* @route '/horizon/api/jobs/pending'
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

const pendingJobs = {
    index: Object.assign(index, index),
}

export default pendingJobs