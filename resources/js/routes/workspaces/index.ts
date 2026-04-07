import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\WorkspaceController::create
* @see app/Http/Controllers/WorkspaceController.php:23
* @route '/workspaces/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/workspaces/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WorkspaceController::create
* @see app/Http/Controllers/WorkspaceController.php:23
* @route '/workspaces/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WorkspaceController::create
* @see app/Http/Controllers/WorkspaceController.php:23
* @route '/workspaces/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WorkspaceController::create
* @see app/Http/Controllers/WorkspaceController.php:23
* @route '/workspaces/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WorkspaceController::create
* @see app/Http/Controllers/WorkspaceController.php:23
* @route '/workspaces/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WorkspaceController::create
* @see app/Http/Controllers/WorkspaceController.php:23
* @route '/workspaces/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WorkspaceController::create
* @see app/Http/Controllers/WorkspaceController.php:23
* @route '/workspaces/create'
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
* @see \App\Http\Controllers\WorkspaceController::store
* @see app/Http/Controllers/WorkspaceController.php:28
* @route '/workspaces'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/workspaces',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WorkspaceController::store
* @see app/Http/Controllers/WorkspaceController.php:28
* @route '/workspaces'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WorkspaceController::store
* @see app/Http/Controllers/WorkspaceController.php:28
* @route '/workspaces'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WorkspaceController::store
* @see app/Http/Controllers/WorkspaceController.php:28
* @route '/workspaces'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WorkspaceController::store
* @see app/Http/Controllers/WorkspaceController.php:28
* @route '/workspaces'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\WorkspaceController::updateCurrent
* @see app/Http/Controllers/WorkspaceController.php:67
* @route '/workspaces/update-current'
*/
export const updateCurrent = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCurrent.url(options),
    method: 'put',
})

updateCurrent.definition = {
    methods: ["put"],
    url: '/workspaces/update-current',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\WorkspaceController::updateCurrent
* @see app/Http/Controllers/WorkspaceController.php:67
* @route '/workspaces/update-current'
*/
updateCurrent.url = (options?: RouteQueryOptions) => {
    return updateCurrent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WorkspaceController::updateCurrent
* @see app/Http/Controllers/WorkspaceController.php:67
* @route '/workspaces/update-current'
*/
updateCurrent.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCurrent.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\WorkspaceController::updateCurrent
* @see app/Http/Controllers/WorkspaceController.php:67
* @route '/workspaces/update-current'
*/
const updateCurrentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCurrent.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WorkspaceController::updateCurrent
* @see app/Http/Controllers/WorkspaceController.php:67
* @route '/workspaces/update-current'
*/
updateCurrentForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCurrent.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateCurrent.form = updateCurrentForm

const workspaces = {
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    updateCurrent: Object.assign(updateCurrent, updateCurrent),
}

export default workspaces