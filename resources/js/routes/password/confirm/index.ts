import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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

const confirm = {
    store: Object.assign(store, store),
}

export default confirm