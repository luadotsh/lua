import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::show
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:19
* @route '/confirm-password'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/confirm-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::show
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:19
* @route '/confirm-password'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::show
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:19
* @route '/confirm-password'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::show
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:19
* @route '/confirm-password'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::show
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:19
* @route '/confirm-password'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::show
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:19
* @route '/confirm-password'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::show
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:19
* @route '/confirm-password'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::store
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:27
* @route '/confirm-password'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/confirm-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::store
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:27
* @route '/confirm-password'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::store
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:27
* @route '/confirm-password'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::store
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:27
* @route '/confirm-password'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::store
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:27
* @route '/confirm-password'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const ConfirmablePasswordController = { show, store }

export default ConfirmablePasswordController