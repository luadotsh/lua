import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::issueToken
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
export const issueToken = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: issueToken.url(options),
    method: 'post',
})

issueToken.definition = {
    methods: ["post"],
    url: '/oauth/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::issueToken
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
issueToken.url = (options?: RouteQueryOptions) => {
    return issueToken.definition.url + queryParams(options)
}

/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::issueToken
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
issueToken.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: issueToken.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::issueToken
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
const issueTokenForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: issueToken.url(options),
    method: 'post',
})

/**
* @see \Laravel\Passport\Http\Controllers\AccessTokenController::issueToken
* @see vendor/laravel/passport/src/Http/Controllers/AccessTokenController.php:25
* @route '/oauth/token'
*/
issueTokenForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: issueToken.url(options),
    method: 'post',
})

issueToken.form = issueTokenForm

const AccessTokenController = { issueToken }

export default AccessTokenController