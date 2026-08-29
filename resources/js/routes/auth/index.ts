import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import invites from './invites'
import social15fd52 from './social'
/**
* @see \App\Http\Controllers\Auth\SocialAuthController::social
* @see app/Http/Controllers/Auth/SocialAuthController.php:26
* @route '/{provider}/login'
*/
export const social = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: social.url(args, options),
    method: 'get',
})

social.definition = {
    methods: ["get","head"],
    url: '/{provider}/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::social
* @see app/Http/Controllers/Auth/SocialAuthController.php:26
* @route '/{provider}/login'
*/
social.url = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return social.definition.url
            .replace('{provider}', parsedArgs.provider.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::social
* @see app/Http/Controllers/Auth/SocialAuthController.php:26
* @route '/{provider}/login'
*/
social.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: social.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::social
* @see app/Http/Controllers/Auth/SocialAuthController.php:26
* @route '/{provider}/login'
*/
social.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: social.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::social
* @see app/Http/Controllers/Auth/SocialAuthController.php:26
* @route '/{provider}/login'
*/
const socialForm = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: social.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::social
* @see app/Http/Controllers/Auth/SocialAuthController.php:26
* @route '/{provider}/login'
*/
socialForm.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: social.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::social
* @see app/Http/Controllers/Auth/SocialAuthController.php:26
* @route '/{provider}/login'
*/
socialForm.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: social.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

social.form = socialForm

const auth = {
    invites: Object.assign(invites, invites),
    social: Object.assign(social, social15fd52),
}

export default auth