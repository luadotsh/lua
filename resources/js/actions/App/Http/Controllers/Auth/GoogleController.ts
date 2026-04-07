import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\GoogleController::redirectToProvider
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
export const redirectToProvider = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirectToProvider.url(options),
    method: 'get',
})

redirectToProvider.definition = {
    methods: ["get","head"],
    url: '/google/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\GoogleController::redirectToProvider
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
redirectToProvider.url = (options?: RouteQueryOptions) => {
    return redirectToProvider.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleController::redirectToProvider
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
redirectToProvider.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirectToProvider.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::redirectToProvider
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
redirectToProvider.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: redirectToProvider.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::redirectToProvider
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
const redirectToProviderForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToProvider.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::redirectToProvider
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
redirectToProviderForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToProvider.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::redirectToProvider
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
redirectToProviderForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToProvider.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

redirectToProvider.form = redirectToProviderForm

/**
* @see \App\Http\Controllers\Auth\GoogleController::handleProviderCallback
* @see app/Http/Controllers/Auth/GoogleController.php:31
* @route '/google/callback'
*/
export const handleProviderCallback = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleProviderCallback.url(options),
    method: 'get',
})

handleProviderCallback.definition = {
    methods: ["get","head"],
    url: '/google/callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\GoogleController::handleProviderCallback
* @see app/Http/Controllers/Auth/GoogleController.php:31
* @route '/google/callback'
*/
handleProviderCallback.url = (options?: RouteQueryOptions) => {
    return handleProviderCallback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleController::handleProviderCallback
* @see app/Http/Controllers/Auth/GoogleController.php:31
* @route '/google/callback'
*/
handleProviderCallback.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleProviderCallback.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::handleProviderCallback
* @see app/Http/Controllers/Auth/GoogleController.php:31
* @route '/google/callback'
*/
handleProviderCallback.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: handleProviderCallback.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::handleProviderCallback
* @see app/Http/Controllers/Auth/GoogleController.php:31
* @route '/google/callback'
*/
const handleProviderCallbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleProviderCallback.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::handleProviderCallback
* @see app/Http/Controllers/Auth/GoogleController.php:31
* @route '/google/callback'
*/
handleProviderCallbackForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleProviderCallback.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::handleProviderCallback
* @see app/Http/Controllers/Auth/GoogleController.php:31
* @route '/google/callback'
*/
handleProviderCallbackForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleProviderCallback.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

handleProviderCallback.form = handleProviderCallbackForm

const GoogleController = { redirectToProvider, handleProviderCallback }

export default GoogleController