import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults, validateParameters } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\RedirectController::password
* @see app/Http/Controllers/RedirectController.php:90
* @route '/{key}/password'
*/
export const password = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: password.url(args, options),
    method: 'get',
})

password.definition = {
    methods: ["get","head"],
    url: '/{key}/password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RedirectController::password
* @see app/Http/Controllers/RedirectController.php:90
* @route '/{key}/password'
*/
password.url = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { key: args }
    }

    if (Array.isArray(args)) {
        args = {
            key: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        key: args.key,
    }

    return password.definition.url
            .replace('{key}', parsedArgs.key.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RedirectController::password
* @see app/Http/Controllers/RedirectController.php:90
* @route '/{key}/password'
*/
password.get = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: password.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RedirectController::password
* @see app/Http/Controllers/RedirectController.php:90
* @route '/{key}/password'
*/
password.head = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: password.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RedirectController::password
* @see app/Http/Controllers/RedirectController.php:90
* @route '/{key}/password'
*/
const passwordForm = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: password.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RedirectController::password
* @see app/Http/Controllers/RedirectController.php:90
* @route '/{key}/password'
*/
passwordForm.get = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: password.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RedirectController::password
* @see app/Http/Controllers/RedirectController.php:90
* @route '/{key}/password'
*/
passwordForm.head = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: password.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

password.form = passwordForm

/**
* @see \App\Http\Controllers\RedirectController::validatePassword
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
export const validatePassword = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: validatePassword.url(args, options),
    method: 'post',
})

validatePassword.definition = {
    methods: ["post"],
    url: '/{key}/password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RedirectController::validatePassword
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
validatePassword.url = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { key: args }
    }

    if (Array.isArray(args)) {
        args = {
            key: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        key: args.key,
    }

    return validatePassword.definition.url
            .replace('{key}', parsedArgs.key.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RedirectController::validatePassword
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
validatePassword.post = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: validatePassword.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RedirectController::validatePassword
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
const validatePasswordForm = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: validatePassword.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RedirectController::validatePassword
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
validatePasswordForm.post = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: validatePassword.url(args, options),
    method: 'post',
})

validatePassword.form = validatePasswordForm

/**
* @see \App\Http\Controllers\RedirectController::redirect
* @see app/Http/Controllers/RedirectController.php:23
* @route '/{key?}'
*/
export const redirect = (args?: { key?: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirect.url(args, options),
    method: 'get',
})

redirect.definition = {
    methods: ["get","head"],
    url: '/{key?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RedirectController::redirect
* @see app/Http/Controllers/RedirectController.php:23
* @route '/{key?}'
*/
redirect.url = (args?: { key?: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { key: args }
    }

    if (Array.isArray(args)) {
        args = {
            key: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "key",
    ])

    const parsedArgs = {
        key: args?.key,
    }

    return redirect.definition.url
            .replace('{key?}', parsedArgs.key?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RedirectController::redirect
* @see app/Http/Controllers/RedirectController.php:23
* @route '/{key?}'
*/
redirect.get = (args?: { key?: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RedirectController::redirect
* @see app/Http/Controllers/RedirectController.php:23
* @route '/{key?}'
*/
redirect.head = (args?: { key?: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: redirect.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RedirectController::redirect
* @see app/Http/Controllers/RedirectController.php:23
* @route '/{key?}'
*/
const redirectForm = (args?: { key?: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RedirectController::redirect
* @see app/Http/Controllers/RedirectController.php:23
* @route '/{key?}'
*/
redirectForm.get = (args?: { key?: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RedirectController::redirect
* @see app/Http/Controllers/RedirectController.php:23
* @route '/{key?}'
*/
redirectForm.head = (args?: { key?: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirect.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

redirect.form = redirectForm

const RedirectController = { password, validatePassword, redirect }

export default RedirectController