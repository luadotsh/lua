import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\InviteController::create
* @see app/Http/Controllers/Setting/InviteController.php:21
* @route '/settings/users/invites/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/settings/users/invites/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\InviteController::create
* @see app/Http/Controllers/Setting/InviteController.php:21
* @route '/settings/users/invites/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\InviteController::create
* @see app/Http/Controllers/Setting/InviteController.php:21
* @route '/settings/users/invites/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\InviteController::create
* @see app/Http/Controllers/Setting/InviteController.php:21
* @route '/settings/users/invites/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\InviteController::create
* @see app/Http/Controllers/Setting/InviteController.php:21
* @route '/settings/users/invites/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\InviteController::create
* @see app/Http/Controllers/Setting/InviteController.php:21
* @route '/settings/users/invites/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\InviteController::create
* @see app/Http/Controllers/Setting/InviteController.php:21
* @route '/settings/users/invites/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Setting\InviteController::store
* @see app/Http/Controllers/Setting/InviteController.php:26
* @route '/settings/users/invites'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/users/invites',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Setting\InviteController::store
* @see app/Http/Controllers/Setting/InviteController.php:26
* @route '/settings/users/invites'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\InviteController::store
* @see app/Http/Controllers/Setting/InviteController.php:26
* @route '/settings/users/invites'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\InviteController::store
* @see app/Http/Controllers/Setting/InviteController.php:26
* @route '/settings/users/invites'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\InviteController::store
* @see app/Http/Controllers/Setting/InviteController.php:26
* @route '/settings/users/invites'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Setting\InviteController::destroy
* @see app/Http/Controllers/Setting/InviteController.php:70
* @route '/settings/users/invites/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/users/invites/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\InviteController::destroy
* @see app/Http/Controllers/Setting/InviteController.php:70
* @route '/settings/users/invites/{id}'
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
* @see \App\Http\Controllers\Setting\InviteController::destroy
* @see app/Http/Controllers/Setting/InviteController.php:70
* @route '/settings/users/invites/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\InviteController::destroy
* @see app/Http/Controllers/Setting/InviteController.php:70
* @route '/settings/users/invites/{id}'
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
* @see \App\Http\Controllers\Setting\InviteController::destroy
* @see app/Http/Controllers/Setting/InviteController.php:70
* @route '/settings/users/invites/{id}'
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

const InviteController = { create, store, destroy }

export default InviteController