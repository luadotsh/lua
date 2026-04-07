import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import photo from './photo'
/**
* @see \App\Http\Controllers\Setting\AccountController::edit
* @see app/Http/Controllers/Setting/AccountController.php:19
* @route '/settings/account'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/account',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\AccountController::edit
* @see app/Http/Controllers/Setting/AccountController.php:19
* @route '/settings/account'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\AccountController::edit
* @see app/Http/Controllers/Setting/AccountController.php:19
* @route '/settings/account'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\AccountController::edit
* @see app/Http/Controllers/Setting/AccountController.php:19
* @route '/settings/account'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\AccountController::edit
* @see app/Http/Controllers/Setting/AccountController.php:19
* @route '/settings/account'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\AccountController::edit
* @see app/Http/Controllers/Setting/AccountController.php:19
* @route '/settings/account'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\AccountController::edit
* @see app/Http/Controllers/Setting/AccountController.php:19
* @route '/settings/account'
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
* @see \App\Http\Controllers\Setting\AccountController::update
* @see app/Http/Controllers/Setting/AccountController.php:30
* @route '/settings/account'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/settings/account',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Setting\AccountController::update
* @see app/Http/Controllers/Setting/AccountController.php:30
* @route '/settings/account'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\AccountController::update
* @see app/Http/Controllers/Setting/AccountController.php:30
* @route '/settings/account'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\AccountController::update
* @see app/Http/Controllers/Setting/AccountController.php:30
* @route '/settings/account'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\AccountController::update
* @see app/Http/Controllers/Setting/AccountController.php:30
* @route '/settings/account'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const account = {
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    photo: Object.assign(photo, photo),
}

export default account