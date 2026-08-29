import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:150
* @route '/.well-known/oauth-authorization-server/{path}'
*/
export const nested = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nested.url(args, options),
    method: 'get',
})

nested.definition = {
    methods: ["get","head"],
    url: '/.well-known/oauth-authorization-server/{path}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:150
* @route '/.well-known/oauth-authorization-server/{path}'
*/
nested.url = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { path: args }
    }

    if (Array.isArray(args)) {
        args = {
            path: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        path: args.path,
    }

    return nested.definition.url
            .replace('{path}', parsedArgs.path.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:150
* @route '/.well-known/oauth-authorization-server/{path}'
*/
nested.get = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nested.url(args, options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:150
* @route '/.well-known/oauth-authorization-server/{path}'
*/
nested.head = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: nested.url(args, options),
    method: 'head',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:150
* @route '/.well-known/oauth-authorization-server/{path}'
*/
const nestedForm = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: nested.url(args, options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:150
* @route '/.well-known/oauth-authorization-server/{path}'
*/
nestedForm.get = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: nested.url(args, options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:150
* @route '/.well-known/oauth-authorization-server/{path}'
*/
nestedForm.head = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: nested.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

nested.form = nestedForm
