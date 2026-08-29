import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\ApproveAuthorizationController::approve
* @see vendor/laravel/passport/src/Http/Controllers/ApproveAuthorizationController.php:25
* @route '/oauth/authorize'
*/
export const approve = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/oauth/authorize',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Passport\Http\Controllers\ApproveAuthorizationController::approve
* @see vendor/laravel/passport/src/Http/Controllers/ApproveAuthorizationController.php:25
* @route '/oauth/authorize'
*/
approve.url = (options?: RouteQueryOptions) => {
    return approve.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\ApproveAuthorizationController::approve
* @see vendor/laravel/passport/src/Http/Controllers/ApproveAuthorizationController.php:25
* @route '/oauth/authorize'
*/
approve.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\ApproveAuthorizationController::approve
* @see vendor/laravel/passport/src/Http/Controllers/ApproveAuthorizationController.php:25
* @route '/oauth/authorize'
*/
const approveForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\ApproveAuthorizationController::approve
* @see vendor/laravel/passport/src/Http/Controllers/ApproveAuthorizationController.php:25
* @route '/oauth/authorize'
*/
approveForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(options),
    method: 'post',
})

approve.form = approveForm

const ApproveAuthorizationController = { approve }

export default ApproveAuthorizationController