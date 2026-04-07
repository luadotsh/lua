import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:46
* @route '/horizon/api/jobs/failed'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/horizon/api/jobs/failed',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:46
* @route '/horizon/api/jobs/failed'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:46
* @route '/horizon/api/jobs/failed'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:46
* @route '/horizon/api/jobs/failed'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:46
* @route '/horizon/api/jobs/failed'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:46
* @route '/horizon/api/jobs/failed'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::index
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:46
* @route '/horizon/api/jobs/failed'
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
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::show
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:101
* @route '/horizon/api/jobs/failed/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/horizon/api/jobs/failed/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::show
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:101
* @route '/horizon/api/jobs/failed/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::show
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:101
* @route '/horizon/api/jobs/failed/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::show
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:101
* @route '/horizon/api/jobs/failed/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::show
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:101
* @route '/horizon/api/jobs/failed/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::show
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:101
* @route '/horizon/api/jobs/failed/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Horizon\Http\Controllers\FailedJobsController::show
* @see vendor/laravel/horizon/src/Http/Controllers/FailedJobsController.php:101
* @route '/horizon/api/jobs/failed/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const failedJobs = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
}

export default failedJobs