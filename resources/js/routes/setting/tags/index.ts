import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\TagController::index
* @see app/Http/Controllers/Setting/TagController.php:17
* @route '/settings/tags'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/tags',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\TagController::index
* @see app/Http/Controllers/Setting/TagController.php:17
* @route '/settings/tags'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\TagController::index
* @see app/Http/Controllers/Setting/TagController.php:17
* @route '/settings/tags'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\TagController::index
* @see app/Http/Controllers/Setting/TagController.php:17
* @route '/settings/tags'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\TagController::index
* @see app/Http/Controllers/Setting/TagController.php:17
* @route '/settings/tags'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\TagController::index
* @see app/Http/Controllers/Setting/TagController.php:17
* @route '/settings/tags'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\TagController::index
* @see app/Http/Controllers/Setting/TagController.php:17
* @route '/settings/tags'
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
* @see \App\Http\Controllers\Setting\TagController::store
* @see app/Http/Controllers/Setting/TagController.php:26
* @route '/settings/tags'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/tags',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Setting\TagController::store
* @see app/Http/Controllers/Setting/TagController.php:26
* @route '/settings/tags'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\TagController::store
* @see app/Http/Controllers/Setting/TagController.php:26
* @route '/settings/tags'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\TagController::store
* @see app/Http/Controllers/Setting/TagController.php:26
* @route '/settings/tags'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\TagController::store
* @see app/Http/Controllers/Setting/TagController.php:26
* @route '/settings/tags'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Setting\TagController::sort
* @see app/Http/Controllers/Setting/TagController.php:85
* @route '/settings/tags/sort'
*/
export const sort = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sort.url(options),
    method: 'post',
})

sort.definition = {
    methods: ["post"],
    url: '/settings/tags/sort',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Setting\TagController::sort
* @see app/Http/Controllers/Setting/TagController.php:85
* @route '/settings/tags/sort'
*/
sort.url = (options?: RouteQueryOptions) => {
    return sort.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\TagController::sort
* @see app/Http/Controllers/Setting/TagController.php:85
* @route '/settings/tags/sort'
*/
sort.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sort.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\TagController::sort
* @see app/Http/Controllers/Setting/TagController.php:85
* @route '/settings/tags/sort'
*/
const sortForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sort.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\TagController::sort
* @see app/Http/Controllers/Setting/TagController.php:85
* @route '/settings/tags/sort'
*/
sortForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sort.url(options),
    method: 'post',
})

sort.form = sortForm

/**
* @see \App\Http\Controllers\Setting\TagController::update
* @see app/Http/Controllers/Setting/TagController.php:56
* @route '/settings/tags/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/settings/tags/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Setting\TagController::update
* @see app/Http/Controllers/Setting/TagController.php:56
* @route '/settings/tags/{id}'
*/
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\TagController::update
* @see app/Http/Controllers/Setting/TagController.php:56
* @route '/settings/tags/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Setting\TagController::update
* @see app/Http/Controllers/Setting/TagController.php:56
* @route '/settings/tags/{id}'
*/
const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\TagController::update
* @see app/Http/Controllers/Setting/TagController.php:56
* @route '/settings/tags/{id}'
*/
updateForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Setting\TagController::destroy
* @see app/Http/Controllers/Setting/TagController.php:74
* @route '/settings/tags/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/tags/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\TagController::destroy
* @see app/Http/Controllers/Setting/TagController.php:74
* @route '/settings/tags/{id}'
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
* @see \App\Http\Controllers\Setting\TagController::destroy
* @see app/Http/Controllers/Setting/TagController.php:74
* @route '/settings/tags/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\TagController::destroy
* @see app/Http/Controllers/Setting/TagController.php:74
* @route '/settings/tags/{id}'
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
* @see \App\Http\Controllers\Setting\TagController::destroy
* @see app/Http/Controllers/Setting/TagController.php:74
* @route '/settings/tags/{id}'
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

const tags = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    sort: Object.assign(sort, sort),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default tags