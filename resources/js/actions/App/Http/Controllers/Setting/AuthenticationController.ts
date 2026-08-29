import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\AuthenticationController::edit
* @see app/Http/Controllers/Setting/AuthenticationController.php:23
* @route '/settings/authentication'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/authentication',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::edit
* @see app/Http/Controllers/Setting/AuthenticationController.php:23
* @route '/settings/authentication'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::edit
* @see app/Http/Controllers/Setting/AuthenticationController.php:23
* @route '/settings/authentication'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::edit
* @see app/Http/Controllers/Setting/AuthenticationController.php:23
* @route '/settings/authentication'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::edit
* @see app/Http/Controllers/Setting/AuthenticationController.php:23
* @route '/settings/authentication'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::edit
* @see app/Http/Controllers/Setting/AuthenticationController.php:23
* @route '/settings/authentication'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::edit
* @see app/Http/Controllers/Setting/AuthenticationController.php:23
* @route '/settings/authentication'
*/
editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::updatePassword
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
export const updatePassword = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatePassword.url(options),
    method: 'put',
})

updatePassword.definition = {
    methods: ["put"],
    url: '/settings/authentication/password',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::updatePassword
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
updatePassword.url = (options?: RouteQueryOptions) => {
    return updatePassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::updatePassword
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
updatePassword.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatePassword.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::updatePassword
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
const updatePasswordForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePassword.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::updatePassword
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
updatePasswordForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePassword.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updatePassword.form = updatePasswordForm

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroyOtherSessions
* @see app/Http/Controllers/Setting/AuthenticationController.php:44
* @route '/settings/authentication/sessions'
*/
export const destroyOtherSessions = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyOtherSessions.url(options),
    method: 'delete',
})

destroyOtherSessions.definition = {
    methods: ["delete"],
    url: '/settings/authentication/sessions',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroyOtherSessions
* @see app/Http/Controllers/Setting/AuthenticationController.php:44
* @route '/settings/authentication/sessions'
*/
destroyOtherSessions.url = (options?: RouteQueryOptions) => {
    return destroyOtherSessions.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroyOtherSessions
* @see app/Http/Controllers/Setting/AuthenticationController.php:44
* @route '/settings/authentication/sessions'
*/
destroyOtherSessions.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyOtherSessions.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroyOtherSessions
* @see app/Http/Controllers/Setting/AuthenticationController.php:44
* @route '/settings/authentication/sessions'
*/
const destroyOtherSessionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyOtherSessions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroyOtherSessions
* @see app/Http/Controllers/Setting/AuthenticationController.php:44
* @route '/settings/authentication/sessions'
*/
destroyOtherSessionsForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyOtherSessions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyOtherSessions.form = destroyOtherSessionsForm

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::disconnectProvider
* @see app/Http/Controllers/Setting/AuthenticationController.php:56
* @route '/settings/authentication/providers/{provider}'
*/
export const disconnectProvider = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: disconnectProvider.url(args, options),
    method: 'delete',
})

disconnectProvider.definition = {
    methods: ["delete"],
    url: '/settings/authentication/providers/{provider}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::disconnectProvider
* @see app/Http/Controllers/Setting/AuthenticationController.php:56
* @route '/settings/authentication/providers/{provider}'
*/
disconnectProvider.url = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return disconnectProvider.definition.url
            .replace('{provider}', parsedArgs.provider.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::disconnectProvider
* @see app/Http/Controllers/Setting/AuthenticationController.php:56
* @route '/settings/authentication/providers/{provider}'
*/
disconnectProvider.delete = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: disconnectProvider.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::disconnectProvider
* @see app/Http/Controllers/Setting/AuthenticationController.php:56
* @route '/settings/authentication/providers/{provider}'
*/
const disconnectProviderForm = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: disconnectProvider.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::disconnectProvider
* @see app/Http/Controllers/Setting/AuthenticationController.php:56
* @route '/settings/authentication/providers/{provider}'
*/
disconnectProviderForm.delete = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: disconnectProvider.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

disconnectProvider.form = disconnectProviderForm

const AuthenticationController = { edit, updatePassword, destroyOtherSessions, disconnectProvider }

export default AuthenticationController