import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\AuthorizationController::authorize
* @see vendor/laravel/passport/src/Http/Controllers/AuthorizationController.php:41
* @route '/oauth/authorize'
*/
export const authorize = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authorize.url(options),
    method: 'get',
})

authorize.definition = {
    methods: ["get","head"],
    url: '/oauth/authorize',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Passport\Http\Controllers\AuthorizationController::authorize
* @see vendor/laravel/passport/src/Http/Controllers/AuthorizationController.php:41
* @route '/oauth/authorize'
*/
authorize.url = (options?: RouteQueryOptions) => {
    return authorize.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\AuthorizationController::authorize
* @see vendor/laravel/passport/src/Http/Controllers/AuthorizationController.php:41
* @route '/oauth/authorize'
*/
authorize.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authorize.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\AuthorizationController::authorize
* @see vendor/laravel/passport/src/Http/Controllers/AuthorizationController.php:41
* @route '/oauth/authorize'
*/
authorize.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: authorize.url(options),
    method: 'head',
})

/**
* @see \Laravel\Passport\Http\Controllers\AuthorizationController::authorize
* @see vendor/laravel/passport/src/Http/Controllers/AuthorizationController.php:41
* @route '/oauth/authorize'
*/
const authorizeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authorize.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\AuthorizationController::authorize
* @see vendor/laravel/passport/src/Http/Controllers/AuthorizationController.php:41
* @route '/oauth/authorize'
*/
authorizeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authorize.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\AuthorizationController::authorize
* @see vendor/laravel/passport/src/Http/Controllers/AuthorizationController.php:41
* @route '/oauth/authorize'
*/
authorizeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authorize.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

authorize.form = authorizeForm

const AuthorizationController = { authorize }

export default AuthorizationController