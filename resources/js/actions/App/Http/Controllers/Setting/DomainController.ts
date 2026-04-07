import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\DomainController::index
* @see app/Http/Controllers/Setting/DomainController.php:21
* @route '/settings/domains'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/domains',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\DomainController::index
* @see app/Http/Controllers/Setting/DomainController.php:21
* @route '/settings/domains'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\DomainController::index
* @see app/Http/Controllers/Setting/DomainController.php:21
* @route '/settings/domains'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::index
* @see app/Http/Controllers/Setting/DomainController.php:21
* @route '/settings/domains'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::index
* @see app/Http/Controllers/Setting/DomainController.php:21
* @route '/settings/domains'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::index
* @see app/Http/Controllers/Setting/DomainController.php:21
* @route '/settings/domains'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::index
* @see app/Http/Controllers/Setting/DomainController.php:21
* @route '/settings/domains'
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
* @see \App\Http\Controllers\Setting\DomainController::store
* @see app/Http/Controllers/Setting/DomainController.php:31
* @route '/settings/domains'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/domains',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Setting\DomainController::store
* @see app/Http/Controllers/Setting/DomainController.php:31
* @route '/settings/domains'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\DomainController::store
* @see app/Http/Controllers/Setting/DomainController.php:31
* @route '/settings/domains'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::store
* @see app/Http/Controllers/Setting/DomainController.php:31
* @route '/settings/domains'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::store
* @see app/Http/Controllers/Setting/DomainController.php:31
* @route '/settings/domains'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Setting\DomainController::update
* @see app/Http/Controllers/Setting/DomainController.php:56
* @route '/settings/domains/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/settings/domains/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Setting\DomainController::update
* @see app/Http/Controllers/Setting/DomainController.php:56
* @route '/settings/domains/{id}'
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
* @see \App\Http\Controllers\Setting\DomainController::update
* @see app/Http/Controllers/Setting/DomainController.php:56
* @route '/settings/domains/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::update
* @see app/Http/Controllers/Setting/DomainController.php:56
* @route '/settings/domains/{id}'
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
* @see \App\Http\Controllers\Setting\DomainController::update
* @see app/Http/Controllers/Setting/DomainController.php:56
* @route '/settings/domains/{id}'
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
* @see \App\Http\Controllers\Setting\DomainController::destroy
* @see app/Http/Controllers/Setting/DomainController.php:75
* @route '/settings/domains/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/domains/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Setting\DomainController::destroy
* @see app/Http/Controllers/Setting/DomainController.php:75
* @route '/settings/domains/{id}'
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
* @see \App\Http\Controllers\Setting\DomainController::destroy
* @see app/Http/Controllers/Setting/DomainController.php:75
* @route '/settings/domains/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::destroy
* @see app/Http/Controllers/Setting/DomainController.php:75
* @route '/settings/domains/{id}'
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
* @see \App\Http\Controllers\Setting\DomainController::destroy
* @see app/Http/Controllers/Setting/DomainController.php:75
* @route '/settings/domains/{id}'
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

/**
* @see \App\Http\Controllers\Setting\DomainController::validateDns
* @see app/Http/Controllers/Setting/DomainController.php:86
* @route '/settings/domains/{id}/validate-dns'
*/
export const validateDns = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: validateDns.url(args, options),
    method: 'get',
})

validateDns.definition = {
    methods: ["get","head"],
    url: '/settings/domains/{id}/validate-dns',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\DomainController::validateDns
* @see app/Http/Controllers/Setting/DomainController.php:86
* @route '/settings/domains/{id}/validate-dns'
*/
validateDns.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return validateDns.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\DomainController::validateDns
* @see app/Http/Controllers/Setting/DomainController.php:86
* @route '/settings/domains/{id}/validate-dns'
*/
validateDns.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: validateDns.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::validateDns
* @see app/Http/Controllers/Setting/DomainController.php:86
* @route '/settings/domains/{id}/validate-dns'
*/
validateDns.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: validateDns.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::validateDns
* @see app/Http/Controllers/Setting/DomainController.php:86
* @route '/settings/domains/{id}/validate-dns'
*/
const validateDnsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validateDns.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::validateDns
* @see app/Http/Controllers/Setting/DomainController.php:86
* @route '/settings/domains/{id}/validate-dns'
*/
validateDnsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validateDns.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\DomainController::validateDns
* @see app/Http/Controllers/Setting/DomainController.php:86
* @route '/settings/domains/{id}/validate-dns'
*/
validateDnsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validateDns.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

validateDns.form = validateDnsForm

const DomainController = { index, store, update, destroy, validateDns }

export default DomainController