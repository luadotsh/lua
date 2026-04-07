import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\WorkspaceController::destroy
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/workspace/photo',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::destroy
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::destroy
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::destroy
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
const destroyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\WorkspaceController::destroy
* @see app/Http/Controllers/Setting/WorkspaceController.php:0
* @route '/settings/workspace/photo'
*/
destroyForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const logo = {
    destroy: Object.assign(destroy, destroy),
}

export default logo