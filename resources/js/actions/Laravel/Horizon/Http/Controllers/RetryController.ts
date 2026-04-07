import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \Laravel\Horizon\Http\Controllers\RetryController::store
* @see vendor/laravel/horizon/src/Http/Controllers/RetryController.php:15
* @route '/horizon/api/jobs/retry/{id}'
*/
export const store = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/horizon/api/jobs/retry/{id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Horizon\Http\Controllers\RetryController::store
* @see vendor/laravel/horizon/src/Http/Controllers/RetryController.php:15
* @route '/horizon/api/jobs/retry/{id}'
*/
store.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Horizon\Http\Controllers\RetryController::store
* @see vendor/laravel/horizon/src/Http/Controllers/RetryController.php:15
* @route '/horizon/api/jobs/retry/{id}'
*/
store.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \Laravel\Horizon\Http\Controllers\RetryController::store
* @see vendor/laravel/horizon/src/Http/Controllers/RetryController.php:15
* @route '/horizon/api/jobs/retry/{id}'
*/
const storeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \Laravel\Horizon\Http\Controllers\RetryController::store
* @see vendor/laravel/horizon/src/Http/Controllers/RetryController.php:15
* @route '/horizon/api/jobs/retry/{id}'
*/
storeForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

const RetryController = { store }

export default RetryController