import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:133
* @route '/.well-known/oauth-protected-resource'
*/
export const protectedResource = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: protectedResource.url(options),
    method: 'get',
})

protectedResource.definition = {
    methods: ["get","head"],
    url: '/.well-known/oauth-protected-resource',
} satisfies RouteDefinition<["get","head"]>

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:133
* @route '/.well-known/oauth-protected-resource'
*/
protectedResource.url = (options?: RouteQueryOptions) => {
    return protectedResource.definition.url + queryParams(options)
}

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:133
* @route '/.well-known/oauth-protected-resource'
*/
protectedResource.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: protectedResource.url(options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:133
* @route '/.well-known/oauth-protected-resource'
*/
protectedResource.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: protectedResource.url(options),
    method: 'head',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:133
* @route '/.well-known/oauth-protected-resource'
*/
const protectedResourceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: protectedResource.url(options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:133
* @route '/.well-known/oauth-protected-resource'
*/
protectedResourceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: protectedResource.url(options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:133
* @route '/.well-known/oauth-protected-resource'
*/
protectedResourceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: protectedResource.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

protectedResource.form = protectedResourceForm

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:138
* @route '/.well-known/oauth-authorization-server'
*/
export const authorizationServer = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authorizationServer.url(options),
    method: 'get',
})

authorizationServer.definition = {
    methods: ["get","head"],
    url: '/.well-known/oauth-authorization-server',
} satisfies RouteDefinition<["get","head"]>

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:138
* @route '/.well-known/oauth-authorization-server'
*/
authorizationServer.url = (options?: RouteQueryOptions) => {
    return authorizationServer.definition.url + queryParams(options)
}

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:138
* @route '/.well-known/oauth-authorization-server'
*/
authorizationServer.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authorizationServer.url(options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:138
* @route '/.well-known/oauth-authorization-server'
*/
authorizationServer.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: authorizationServer.url(options),
    method: 'head',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:138
* @route '/.well-known/oauth-authorization-server'
*/
const authorizationServerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authorizationServer.url(options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:138
* @route '/.well-known/oauth-authorization-server'
*/
authorizationServerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authorizationServer.url(options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:138
* @route '/.well-known/oauth-authorization-server'
*/
authorizationServerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authorizationServer.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

authorizationServer.form = authorizationServerForm
