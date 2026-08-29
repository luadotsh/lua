import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \Laravel\Mcp\Server\Http\Controllers\OAuthRegisterController::__invoke
* @see vendor/laravel/mcp/src/Server/Http/Controllers/OAuthRegisterController.php:24
* @route '/oauth/register'
*/
const OAuthRegisterController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: OAuthRegisterController.url(options),
    method: 'post',
})

OAuthRegisterController.definition = {
    methods: ["post"],
    url: '/oauth/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Mcp\Server\Http\Controllers\OAuthRegisterController::__invoke
* @see vendor/laravel/mcp/src/Server/Http/Controllers/OAuthRegisterController.php:24
* @route '/oauth/register'
*/
OAuthRegisterController.url = (options?: RouteQueryOptions) => {
    return OAuthRegisterController.definition.url + queryParams(options)
}

/**
* @see \Laravel\Mcp\Server\Http\Controllers\OAuthRegisterController::__invoke
* @see vendor/laravel/mcp/src/Server/Http/Controllers/OAuthRegisterController.php:24
* @route '/oauth/register'
*/
OAuthRegisterController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: OAuthRegisterController.url(options),
    method: 'post',
})

/**
* @see \Laravel\Mcp\Server\Http\Controllers\OAuthRegisterController::__invoke
* @see vendor/laravel/mcp/src/Server/Http/Controllers/OAuthRegisterController.php:24
* @route '/oauth/register'
*/
const OAuthRegisterControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: OAuthRegisterController.url(options),
    method: 'post',
})

/**
* @see \Laravel\Mcp\Server\Http\Controllers\OAuthRegisterController::__invoke
* @see vendor/laravel/mcp/src/Server/Http/Controllers/OAuthRegisterController.php:24
* @route '/oauth/register'
*/
OAuthRegisterControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: OAuthRegisterController.url(options),
    method: 'post',
})

OAuthRegisterController.form = OAuthRegisterControllerForm

export default OAuthRegisterController