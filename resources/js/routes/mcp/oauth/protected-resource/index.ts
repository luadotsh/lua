import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:142
* @route '/.well-known/oauth-protected-resource/{path}'
*/
export const nested = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nested.url(args, options),
    method: 'get',
})

nested.definition = {
    methods: ["get","head"],
    url: '/.well-known/oauth-protected-resource/{path}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:142
* @route '/.well-known/oauth-protected-resource/{path}'
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
* @see vendor/laravel/mcp/src/Server/Registrar.php:142
* @route '/.well-known/oauth-protected-resource/{path}'
*/
nested.get = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nested.url(args, options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:142
* @route '/.well-known/oauth-protected-resource/{path}'
*/
nested.head = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: nested.url(args, options),
    method: 'head',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:142
* @route '/.well-known/oauth-protected-resource/{path}'
*/
const nestedForm = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: nested.url(args, options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:142
* @route '/.well-known/oauth-protected-resource/{path}'
*/
nestedForm.get = (args: { path: string | number } | [path: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: nested.url(args, options),
    method: 'get',
})

/**
* @see vendor/laravel/mcp/src/Server/Registrar.php:142
* @route '/.well-known/oauth-protected-resource/{path}'
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
