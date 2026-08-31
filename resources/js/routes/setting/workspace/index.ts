import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import logo from './logo'
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

const workspace = {
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    logo: Object.assign(logo, logo),
}

export default workspace