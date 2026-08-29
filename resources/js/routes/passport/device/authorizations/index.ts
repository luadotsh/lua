import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
export const authorize = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authorize.url(options),
    method: 'get',
})

authorize.definition = {
    methods: ["get","head"],
    url: '/oauth/device/authorize',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
authorize.url = (options?: RouteQueryOptions) => {
    return authorize.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
authorize.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authorize.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
authorize.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: authorize.url(options),
    method: 'head',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
const authorizeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authorize.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
*/
authorizeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authorize.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceAuthorizationController.php:31
* @route '/oauth/device/authorize'
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

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
export const approve = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/oauth/device/authorize',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
approve.url = (options?: RouteQueryOptions) => {
    return approve.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
approve.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
const approveForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\ApproveDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/ApproveDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
approveForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(options),
    method: 'post',
})

approve.form = approveForm

/**
* @see \Laravel\Passport\Http\Controllers\DenyDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DenyDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
export const deny = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deny.url(options),
    method: 'delete',
})

deny.definition = {
    methods: ["delete"],
    url: '/oauth/device/authorize',
} satisfies RouteDefinition<["delete"]>

/**
* @see \Laravel\Passport\Http\Controllers\DenyDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DenyDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
deny.url = (options?: RouteQueryOptions) => {
    return deny.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\DenyDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DenyDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
*/
deny.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deny.url(options),
    method: 'delete',
})

/**
* @see \Laravel\Passport\Http\Controllers\DenyDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DenyDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
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
* @see \Laravel\Passport\Http\Controllers\DenyDeviceAuthorizationController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DenyDeviceAuthorizationController.php:24
* @route '/oauth/device/authorize'
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

const authorizations = {
    authorize: Object.assign(authorize, authorize),
    approve: Object.assign(approve, approve),
    deny: Object.assign(deny, deny),
}

export default authorizations