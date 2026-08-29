import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirectToProvider
* @see app/Http/Controllers/Auth/SocialAuthController.php:25
* @route '/{provider}/login'
*/
export const redirectToProvider = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirectToProvider.url(args, options),
    method: 'get',
})

redirectToProvider.definition = {
    methods: ["get","head"],
    url: '/{provider}/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirectToProvider
* @see app/Http/Controllers/Auth/SocialAuthController.php:25
* @route '/{provider}/login'
*/
redirectToProvider.url = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { provider: args }
    }

    if (Array.isArray(args)) {
        args = {
            provider: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        provider: args.provider,
    }

    return redirectToProvider.definition.url
            .replace('{provider}', parsedArgs.provider.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirectToProvider
* @see app/Http/Controllers/Auth/SocialAuthController.php:25
* @route '/{provider}/login'
*/
redirectToProvider.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirectToProvider.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirectToProvider
* @see app/Http/Controllers/Auth/SocialAuthController.php:25
* @route '/{provider}/login'
*/
redirectToProvider.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: redirectToProvider.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirectToProvider
* @see app/Http/Controllers/Auth/SocialAuthController.php:25
* @route '/{provider}/login'
*/
const redirectToProviderForm = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToProvider.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirectToProvider
* @see app/Http/Controllers/Auth/SocialAuthController.php:25
* @route '/{provider}/login'
*/
redirectToProviderForm.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToProvider.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirectToProvider
* @see app/Http/Controllers/Auth/SocialAuthController.php:25
* @route '/{provider}/login'
*/
redirectToProviderForm.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToProvider.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

redirectToProvider.form = redirectToProviderForm

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::handleProviderCallback
* @see app/Http/Controllers/Auth/SocialAuthController.php:42
* @route '/{provider}/callback'
*/
export const handleProviderCallback = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleProviderCallback.url(args, options),
    method: 'get',
})

handleProviderCallback.definition = {
    methods: ["get","head"],
    url: '/{provider}/callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::handleProviderCallback
* @see app/Http/Controllers/Auth/SocialAuthController.php:42
* @route '/{provider}/callback'
*/
handleProviderCallback.url = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { provider: args }
    }

    if (Array.isArray(args)) {
        args = {
            provider: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        provider: args.provider,
    }

    return handleProviderCallback.definition.url
            .replace('{provider}', parsedArgs.provider.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::handleProviderCallback
* @see app/Http/Controllers/Auth/SocialAuthController.php:42
* @route '/{provider}/callback'
*/
handleProviderCallback.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleProviderCallback.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::handleProviderCallback
* @see app/Http/Controllers/Auth/SocialAuthController.php:42
* @route '/{provider}/callback'
*/
handleProviderCallback.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: handleProviderCallback.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::handleProviderCallback
* @see app/Http/Controllers/Auth/SocialAuthController.php:42
* @route '/{provider}/callback'
*/
const handleProviderCallbackForm = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleProviderCallback.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::handleProviderCallback
* @see app/Http/Controllers/Auth/SocialAuthController.php:42
* @route '/{provider}/callback'
*/
handleProviderCallbackForm.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleProviderCallback.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::handleProviderCallback
* @see app/Http/Controllers/Auth/SocialAuthController.php:42
* @route '/{provider}/callback'
*/
handleProviderCallbackForm.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleProviderCallback.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

handleProviderCallback.form = handleProviderCallbackForm

const SocialAuthController = { redirectToProvider, handleProviderCallback }

export default SocialAuthController