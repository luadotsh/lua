import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import token0f65b5 from './token'
import authorizations from './authorizations'
import deviceD55896 from './device'
/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::token
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
export const token = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token.url(options),
    method: 'post',
})

token.definition = {
    methods: ["post"],
    url: '/oauth/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::token
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
token.url = (options?: RouteQueryOptions) => {
    return token.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::token
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
token.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::token
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
const tokenForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: token.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::token
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
tokenForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: token.url(options),
    method: 'post',
})

token.form = tokenForm

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
export const device = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: device.url(options),
    method: 'get',
})

device.definition = {
    methods: ["get","head"],
    url: '/oauth/device',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
device.url = (options?: RouteQueryOptions) => {
    return device.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
device.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: device.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
device.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: device.url(options),
    method: 'head',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
const deviceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: device.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
deviceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: device.url(options),
    method: 'get',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceUserCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceUserCodeController.php:14
* @route '/oauth/device'
*/
deviceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: device.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

device.form = deviceForm

const passport = {
    token: Object.assign(token, token0f65b5),
    authorizations: Object.assign(authorizations, authorizations),
    device: Object.assign(device, deviceD55896),
}

export default passport