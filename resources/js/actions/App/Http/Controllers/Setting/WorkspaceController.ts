import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\WorkspaceController::edit
* @see app/Http/Controllers/Setting/WorkspaceController.php:15
* @route '/settings/workspace'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/workspace',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::edit
* @see app/Http/Controllers/Setting/WorkspaceController.php:15
* @route '/settings/workspace'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::edit
* @see app/Http/Controllers/Setting/WorkspaceController.php:15
* @route '/settings/workspace'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::edit
* @see app/Http/Controllers/Setting/WorkspaceController.php:15
* @route '/settings/workspace'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::edit
* @see app/Http/Controllers/Setting/WorkspaceController.php:15
* @route '/settings/workspace'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::edit
* @see app/Http/Controllers/Setting/WorkspaceController.php:15
* @route '/settings/workspace'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::edit
* @see app/Http/Controllers/Setting/WorkspaceController.php:15
* @route '/settings/workspace'
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
* @see \App\Http\Controllers\Setting\WorkspaceController::update
* @see app/Http/Controllers/Setting/WorkspaceController.php:20
* @route '/settings/workspace'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/settings/workspace',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::update
* @see app/Http/Controllers/Setting/WorkspaceController.php:20
* @route '/settings/workspace'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::update
* @see app/Http/Controllers/Setting/WorkspaceController.php:20
* @route '/settings/workspace'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::update
* @see app/Http/Controllers/Setting/WorkspaceController.php:20
* @route '/settings/workspace'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::update
* @see app/Http/Controllers/Setting/WorkspaceController.php:20
* @route '/settings/workspace'
*/
updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::deleteLogo
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
export const deleteLogo = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteLogo.url(options),
    method: 'delete',
})

deleteLogo.definition = {
    methods: ["delete"],
    url: '/settings/workspace/photo',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::deleteLogo
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
deleteLogo.url = (options?: RouteQueryOptions) => {
    return deleteLogo.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::deleteLogo
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
deleteLogo.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteLogo.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::deleteLogo
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
const deleteLogoForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteLogo.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::deleteLogo
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
deleteLogoForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteLogo.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

deleteLogo.form = deleteLogoForm

const WorkspaceController = { edit, update, deleteLogo }

export default WorkspaceController