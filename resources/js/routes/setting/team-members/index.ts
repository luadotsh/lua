import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\TeamMemberController::index
* @see app/Http/Controllers/Setting/TeamMemberController.php:20
* @route '/settings/users'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::index
* @see app/Http/Controllers/Setting/TeamMemberController.php:20
* @route '/settings/users'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::index
* @see app/Http/Controllers/Setting/TeamMemberController.php:20
* @route '/settings/users'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::index
* @see app/Http/Controllers/Setting/TeamMemberController.php:20
* @route '/settings/users'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::index
* @see app/Http/Controllers/Setting/TeamMemberController.php:20
* @route '/settings/users'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::index
* @see app/Http/Controllers/Setting/TeamMemberController.php:20
* @route '/settings/users'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::index
* @see app/Http/Controllers/Setting/TeamMemberController.php:20
* @route '/settings/users'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::role
* @see app/Http/Controllers/Setting/TeamMemberController.php:42
* @route '/settings/users/{id}/role'
*/
export const role = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: role.url(args, options),
    method: 'put',
})

role.definition = {
    methods: ["put"],
    url: '/settings/users/{id}/role',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::role
* @see app/Http/Controllers/Setting/TeamMemberController.php:42
* @route '/settings/users/{id}/role'
*/
role.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return role.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::role
* @see app/Http/Controllers/Setting/TeamMemberController.php:42
* @route '/settings/users/{id}/role'
*/
role.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: role.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::role
* @see app/Http/Controllers/Setting/TeamMemberController.php:42
* @route '/settings/users/{id}/role'
*/
const roleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: role.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::role
* @see app/Http/Controllers/Setting/TeamMemberController.php:42
* @route '/settings/users/{id}/role'
*/
roleForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: role.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

role.form = roleForm

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::leave
* @see app/Http/Controllers/Setting/TeamMemberController.php:81
* @route '/settings/users/leave'
*/
export const leave = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: leave.url(options),
    method: 'delete',
})

leave.definition = {
    methods: ["delete"],
    url: '/settings/users/leave',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::leave
* @see app/Http/Controllers/Setting/TeamMemberController.php:81
* @route '/settings/users/leave'
*/
leave.url = (options?: RouteQueryOptions) => {
    return leave.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::leave
* @see app/Http/Controllers/Setting/TeamMemberController.php:81
* @route '/settings/users/leave'
*/
leave.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: leave.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::leave
* @see app/Http/Controllers/Setting/TeamMemberController.php:81
* @route '/settings/users/leave'
*/
const leaveForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: leave.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::leave
* @see app/Http/Controllers/Setting/TeamMemberController.php:81
* @route '/settings/users/leave'
*/
leaveForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: leave.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

leave.form = leaveForm

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::destroy
* @see app/Http/Controllers/Setting/TeamMemberController.php:63
* @route '/settings/users/{id}/remove-from-team'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/users/{id}/remove-from-team',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::destroy
* @see app/Http/Controllers/Setting/TeamMemberController.php:63
* @route '/settings/users/{id}/remove-from-team'
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
* @see \App\Http\Controllers\Setting\TeamMemberController::destroy
* @see app/Http/Controllers/Setting/TeamMemberController.php:63
* @route '/settings/users/{id}/remove-from-team'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\TeamMemberController::destroy
* @see app/Http/Controllers/Setting/TeamMemberController.php:63
* @route '/settings/users/{id}/remove-from-team'
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
* @see \App\Http\Controllers\Setting\TeamMemberController::destroy
* @see app/Http/Controllers/Setting/TeamMemberController.php:63
* @route '/settings/users/{id}/remove-from-team'
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

const teamMembers = {
    index: Object.assign(index, index),
    role: Object.assign(role, role),
    leave: Object.assign(leave, leave),
    destroy: Object.assign(destroy, destroy),
}

export default teamMembers