import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
const ApproveDeviceAuthorizationController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ApproveDeviceAuthorizationController.url(options),
    method: 'post',
})

ApproveDeviceAuthorizationController.definition = {
    methods: ["post"],
    url: '/oauth/device/authorize',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
ApproveDeviceAuthorizationController.url = (options?: RouteQueryOptions) => {
    return ApproveDeviceAuthorizationController.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
ApproveDeviceAuthorizationController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ApproveDeviceAuthorizationController.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
const ApproveDeviceAuthorizationControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ApproveDeviceAuthorizationController.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
ApproveDeviceAuthorizationControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ApproveDeviceAuthorizationController.url(options),
    method: 'post',
})

ApproveDeviceAuthorizationController.form = ApproveDeviceAuthorizationControllerForm

export default ApproveDeviceAuthorizationController