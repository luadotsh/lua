import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\TransientTokenController::refresh
* @see vendor/laravel/passport/src/Http/Controllers/TransientTokenController.php:22
* @route '/oauth/token/refresh'
*/
export const refresh = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refresh.url(options),
    method: 'post',
})

refresh.definition = {
    methods: ["post"],
    url: '/oauth/token/refresh',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Passport\Http\Controllers\TransientTokenController::refresh
* @see vendor/laravel/passport/src/Http/Controllers/TransientTokenController.php:22
* @route '/oauth/token/refresh'
*/
refresh.url = (options?: RouteQueryOptions) => {
    return refresh.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\TransientTokenController::refresh
* @see vendor/laravel/passport/src/Http/Controllers/TransientTokenController.php:22
* @route '/oauth/token/refresh'
*/
refresh.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refresh.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\TransientTokenController::refresh
* @see vendor/laravel/passport/src/Http/Controllers/TransientTokenController.php:22
* @route '/oauth/token/refresh'
*/
const refreshForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refresh.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\TransientTokenController::refresh
* @see vendor/laravel/passport/src/Http/Controllers/TransientTokenController.php:22
* @route '/oauth/token/refresh'
*/
refreshForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refresh.url(options),
    method: 'post',
})

refresh.form = refreshForm

const token = {
    refresh: Object.assign(refresh, refresh),
}

export default token