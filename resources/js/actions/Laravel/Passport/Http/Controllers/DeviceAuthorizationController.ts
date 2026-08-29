import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
const DeviceAuthorizationController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DeviceAuthorizationController.url(options),
    method: 'get',
})

DeviceAuthorizationController.definition = {
    methods: ["get","head"],
    url: '/oauth/device/authorize',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
DeviceAuthorizationController.url = (options?: RouteQueryOptions) => {
    return DeviceAuthorizationController.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
DeviceAuthorizationController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DeviceAuthorizationController.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
DeviceAuthorizationController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DeviceAuthorizationController.url(options),
    method: 'head',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
const DeviceAuthorizationControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DeviceAuthorizationController.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
DeviceAuthorizationControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DeviceAuthorizationController.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
DeviceAuthorizationControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DeviceAuthorizationController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

DeviceAuthorizationController.form = DeviceAuthorizationControllerForm

export default DeviceAuthorizationController