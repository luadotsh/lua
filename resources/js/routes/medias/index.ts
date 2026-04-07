import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\MediaController::download
* @see app/Http/Controllers/MediaController.php:95
* @route '/medias/{id}/download'
*/
export const download = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/medias/{id}/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MediaController::download
* @see app/Http/Controllers/MediaController.php:95
* @route '/medias/{id}/download'
*/
download.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return download.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MediaController::download
* @see app/Http/Controllers/MediaController.php:95
* @route '/medias/{id}/download'
*/
download.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MediaController::download
* @see app/Http/Controllers/MediaController.php:95
* @route '/medias/{id}/download'
*/
download.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MediaController::download
* @see app/Http/Controllers/MediaController.php:95
* @route '/medias/{id}/download'
*/
const downloadForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MediaController::download
* @see app/Http/Controllers/MediaController.php:95
* @route '/medias/{id}/download'
*/
downloadForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MediaController::download
* @see app/Http/Controllers/MediaController.php:95
* @route '/medias/{id}/download'
*/
downloadForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm

/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:16
* @route '/medias'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/medias',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:16
* @route '/medias'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:16
* @route '/medias'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:16
* @route '/medias'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:16
* @route '/medias'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\MediaController::sort
* @see app/Http/Controllers/MediaController.php:44
* @route '/medias/sort'
*/
export const sort = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sort.url(options),
    method: 'post',
})

sort.definition = {
    methods: ["post"],
    url: '/medias/sort',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MediaController::sort
* @see app/Http/Controllers/MediaController.php:44
* @route '/medias/sort'
*/
sort.url = (options?: RouteQueryOptions) => {
    return sort.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MediaController::sort
* @see app/Http/Controllers/MediaController.php:44
* @route '/medias/sort'
*/
sort.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sort.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::sort
* @see app/Http/Controllers/MediaController.php:44
* @route '/medias/sort'
*/
const sortForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sort.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::sort
* @see app/Http/Controllers/MediaController.php:44
* @route '/medias/sort'
*/
sortForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sort.url(options),
    method: 'post',
})

sort.form = sortForm

/**
* @see \App\Http\Controllers\MediaController::thumbmail
* @see app/Http/Controllers/MediaController.php:75
* @route '/medias/{modelId}/thumbnail/{id}'
*/
export const thumbmail = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: thumbmail.url(args, options),
    method: 'post',
})

thumbmail.definition = {
    methods: ["post"],
    url: '/medias/{modelId}/thumbnail/{id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MediaController::thumbmail
* @see app/Http/Controllers/MediaController.php:75
* @route '/medias/{modelId}/thumbnail/{id}'
*/
thumbmail.url = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            modelId: args[0],
            id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        modelId: args.modelId,
        id: args.id,
    }

    return thumbmail.definition.url
            .replace('{modelId}', parsedArgs.modelId.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MediaController::thumbmail
* @see app/Http/Controllers/MediaController.php:75
* @route '/medias/{modelId}/thumbnail/{id}'
*/
thumbmail.post = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: thumbmail.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::thumbmail
* @see app/Http/Controllers/MediaController.php:75
* @route '/medias/{modelId}/thumbnail/{id}'
*/
const thumbmailForm = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: thumbmail.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::thumbmail
* @see app/Http/Controllers/MediaController.php:75
* @route '/medias/{modelId}/thumbnail/{id}'
*/
thumbmailForm.post = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: thumbmail.url(args, options),
    method: 'post',
})

thumbmail.form = thumbmailForm

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:101
* @route '/medias/{modelId}/{id}'
*/
export const destroy = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/medias/{modelId}/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:101
* @route '/medias/{modelId}/{id}'
*/
destroy.url = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            modelId: args[0],
            id: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        modelId: args.modelId,
        id: args.id,
    }

    return destroy.definition.url
            .replace('{modelId}', parsedArgs.modelId.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:101
* @route '/medias/{modelId}/{id}'
*/
destroy.delete = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:101
* @route '/medias/{modelId}/{id}'
*/
const destroyForm = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:101
* @route '/medias/{modelId}/{id}'
*/
destroyForm.delete = (args: { modelId: string | number, id: string | number } | [modelId: string | number, id: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const medias = {
    download: Object.assign(download, download),
    store: Object.assign(store, store),
    sort: Object.assign(sort, sort),
    thumbmail: Object.assign(thumbmail, thumbmail),
    destroy: Object.assign(destroy, destroy),
}

export default medias