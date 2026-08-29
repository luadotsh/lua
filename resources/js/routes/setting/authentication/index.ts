import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import sessions from './sessions'
import providers from './providers'
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
* @see \App\Http\Controllers\Setting\AuthenticationController::password
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
export const password = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: password.url(options),
    method: 'put',
})

password.definition = {
    methods: ["put"],
    url: '/settings/authentication/password',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::password
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
password.url = (options?: RouteQueryOptions) => {
    return password.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::password
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
password.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: password.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::password
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
const passwordForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: password.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::password
* @see app/Http/Controllers/Setting/AuthenticationController.php:34
* @route '/settings/authentication/password'
*/
passwordForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: password.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

password.form = passwordForm

const authentication = {
    edit: Object.assign(edit, edit),
    password: Object.assign(password, password),
    sessions: Object.assign(sessions, sessions),
    providers: Object.assign(providers, providers),
}

export default authentication