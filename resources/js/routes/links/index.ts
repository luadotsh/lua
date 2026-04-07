import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults, validateParameters } from './../../wayfinder'
import password9cfa90 from './password'
/**
* @see \App\Http\Controllers\LinkController::index
* @see app/Http/Controllers/LinkController.php:23
* @route '/links/{id?}'
*/
export const index = (args?: { id?: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/links/{id?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LinkController::index
* @see app/Http/Controllers/LinkController.php:23
* @route '/links/{id?}'
*/
index.url = (args?: { id?: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "id",
    ])

    const parsedArgs = {
        id: args?.id,
    }

    return index.definition.url
            .replace('{id?}', parsedArgs.id?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LinkController::index
* @see app/Http/Controllers/LinkController.php:23
* @route '/links/{id?}'
*/
index.get = (args?: { id?: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LinkController::index
* @see app/Http/Controllers/LinkController.php:23
* @route '/links/{id?}'
*/
index.head = (args?: { id?: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LinkController::index
* @see app/Http/Controllers/LinkController.php:23
* @route '/links/{id?}'
*/
const indexForm = (args?: { id?: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LinkController::index
* @see app/Http/Controllers/LinkController.php:23
* @route '/links/{id?}'
*/
indexForm.get = (args?: { id?: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LinkController::index
* @see app/Http/Controllers/LinkController.php:23
* @route '/links/{id?}'
*/
indexForm.head = (args?: { id?: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\LinkController::store
* @see app/Http/Controllers/LinkController.php:63
* @route '/links'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/links',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LinkController::store
* @see app/Http/Controllers/LinkController.php:63
* @route '/links'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LinkController::store
* @see app/Http/Controllers/LinkController.php:63
* @route '/links'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LinkController::store
* @see app/Http/Controllers/LinkController.php:63
* @route '/links'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LinkController::store
* @see app/Http/Controllers/LinkController.php:63
* @route '/links'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\LinkController::update
* @see app/Http/Controllers/LinkController.php:103
* @route '/links/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/links/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LinkController::update
* @see app/Http/Controllers/LinkController.php:103
* @route '/links/{id}'
*/
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LinkController::update
* @see app/Http/Controllers/LinkController.php:103
* @route '/links/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\LinkController::update
* @see app/Http/Controllers/LinkController.php:103
* @route '/links/{id}'
*/
const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LinkController::update
* @see app/Http/Controllers/LinkController.php:103
* @route '/links/{id}'
*/
updateForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\LinkController::destroy
* @see app/Http/Controllers/LinkController.php:137
* @route '/links/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/links/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LinkController::destroy
* @see app/Http/Controllers/LinkController.php:137
* @route '/links/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LinkController::destroy
* @see app/Http/Controllers/LinkController.php:137
* @route '/links/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\LinkController::destroy
* @see app/Http/Controllers/LinkController.php:137
* @route '/links/{id}'
*/
const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LinkController::destroy
* @see app/Http/Controllers/LinkController.php:137
* @route '/links/{id}'
*/
destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

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

const links = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    password: Object.assign(password, password9cfa90),
    redirect: Object.assign(redirect, redirect),
}

export default links