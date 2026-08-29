import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import authorizations from './authorizations'
/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
export const code = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: code.url(options),
    method: 'post',
})

code.definition = {
    methods: ["post"],
    url: '/oauth/device/code',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
code.url = (options?: RouteQueryOptions) => {
    return code.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
code.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: code.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
const codeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: code.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\DeviceCodeController::__invoke
* @see vendor/laravel/passport/src/Http/Controllers/DeviceCodeController.php:25
* @route '/oauth/device/code'
*/
codeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: code.url(options),
    method: 'post',
})

code.form = codeForm

const device = {
    code: Object.assign(code, code),
    authorizations: Object.assign(authorizations, authorizations),
}

export default device