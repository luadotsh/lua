import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
const DeviceUserCodeController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DeviceUserCodeController.url(options),
    method: 'get',
})

DeviceUserCodeController.definition = {
    methods: ["get","head"],
    url: '/oauth/device',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
DeviceUserCodeController.url = (options?: RouteQueryOptions) => {
    return DeviceUserCodeController.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
DeviceUserCodeController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DeviceUserCodeController.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
DeviceUserCodeController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DeviceUserCodeController.url(options),
    method: 'head',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
const DeviceUserCodeControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DeviceUserCodeController.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
DeviceUserCodeControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DeviceUserCodeController.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
DeviceUserCodeControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DeviceUserCodeController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

DeviceUserCodeController.form = DeviceUserCodeControllerForm

export default DeviceUserCodeController