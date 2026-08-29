import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:14
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
* @see app/Http/Controllers/MediaController.php:14
* @route '/medias'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:14
* @route '/medias'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:14
* @route '/medias'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MediaController::store
* @see app/Http/Controllers/MediaController.php:14
* @route '/medias'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:25
* @route '/medias/{media}'
*/
export const destroy = (args: { media: string | { id: string } } | [media: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/medias/{media}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:25
* @route '/medias/{media}'
*/
destroy.url = (args: { media: string | { id: string } } | [media: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { media: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { media: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            media: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        media: typeof args.media === 'object'
        ? args.media.id
        : args.media,
    }

    return destroy.definition.url
            .replace('{media}', parsedArgs.media.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:25
* @route '/medias/{media}'
*/
destroy.delete = (args: { media: string | { id: string } } | [media: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\MediaController::destroy
* @see app/Http/Controllers/MediaController.php:25
* @route '/medias/{media}'
*/
const destroyForm = (args: { media: string | { id: string } } | [media: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/MediaController.php:25
* @route '/medias/{media}'
*/
destroyForm.delete = (args: { media: string | { id: string } } | [media: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const MediaController = { store, destroy }

export default MediaController