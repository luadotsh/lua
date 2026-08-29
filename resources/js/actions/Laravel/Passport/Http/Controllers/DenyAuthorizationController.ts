import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\DenyAuthorizationController::deny
* @see vendor/laravel/passport/src/Http/Controllers/DenyAuthorizationController.php:25
* @route '/oauth/authorize'
*/
export const deny = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deny.url(options),
    method: 'delete',
})

deny.definition = {
    methods: ["delete"],
    url: '/oauth/authorize',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Laravel\Passport\Http\Controllers\DenyAuthorizationController::deny
* @see vendor/laravel/passport/src/Http/Controllers/DenyAuthorizationController.php:25
* @route '/oauth/authorize'
*/
deny.url = (options?: RouteQueryOptions) => {
    return deny.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\DenyAuthorizationController::deny
* @see vendor/laravel/passport/src/Http/Controllers/DenyAuthorizationController.php:25
* @route '/oauth/authorize'
*/
deny.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deny.url(options),
    method: 'delete',
})

/**
* @see \Laravel\Passport\Http\Controllers\DenyAuthorizationController::deny
* @see vendor/laravel/passport/src/Http/Controllers/DenyAuthorizationController.php:25
* @route '/oauth/authorize'
*/
const denyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deny.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\DenyAuthorizationController::deny
* @see vendor/laravel/passport/src/Http/Controllers/DenyAuthorizationController.php:25
* @route '/oauth/authorize'
*/
denyForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deny.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

deny.form = denyForm

const DenyAuthorizationController = { deny }

export default DenyAuthorizationController