import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroy
* @see app/Http/Controllers/Setting/AuthenticationController.php:55
* @route '/settings/authentication/sessions'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/authentication/sessions',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroy
* @see app/Http/Controllers/Setting/AuthenticationController.php:55
* @route '/settings/authentication/sessions'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroy
* @see app/Http/Controllers/Setting/AuthenticationController.php:55
* @route '/settings/authentication/sessions'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\AuthenticationController::destroy
* @see app/Http/Controllers/Setting/AuthenticationController.php:55
* @route '/settings/authentication/sessions'
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
* @see \App\Http\Controllers\Setting\AuthenticationController::destroy
* @see app/Http/Controllers/Setting/AuthenticationController.php:55
* @route '/settings/authentication/sessions'
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

const sessions = {
    destroy: Object.assign(destroy, destroy),
}

export default sessions