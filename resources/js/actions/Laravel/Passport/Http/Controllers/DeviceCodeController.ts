import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
const DeviceCodeController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: DeviceCodeController.url(options),
    method: 'post',
})

DeviceCodeController.definition = {
    methods: ["post"],
    url: '/oauth/device/code',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
DeviceCodeController.url = (options?: RouteQueryOptions) => {
    return DeviceCodeController.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
DeviceCodeController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: DeviceCodeController.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
const DeviceCodeControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: DeviceCodeController.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
DeviceCodeControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: DeviceCodeController.url(options),
    method: 'post',
})

DeviceCodeController.form = DeviceCodeControllerForm

export default DeviceCodeController