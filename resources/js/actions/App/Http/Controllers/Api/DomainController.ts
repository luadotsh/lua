import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\DomainController::validate
* @see app/Http/Controllers/Api/DomainController.php:24
* @route '/api/domains/validate'
*/
export const validate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: validate.url(options),
    method: 'get',
})

validate.definition = {
    methods: ["get","head"],
    url: '/api/domains/validate',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DomainController::validate
* @see app/Http/Controllers/Api/DomainController.php:24
* @route '/api/domains/validate'
*/
validate.url = (options?: RouteQueryOptions) => {
    return validate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DomainController::validate
* @see app/Http/Controllers/Api/DomainController.php:24
* @route '/api/domains/validate'
*/
validate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: validate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DomainController::validate
* @see app/Http/Controllers/Api/DomainController.php:24
* @route '/api/domains/validate'
*/
validate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: validate.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\DomainController::validate
* @see app/Http/Controllers/Api/DomainController.php:24
* @route '/api/domains/validate'
*/
const validateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DomainController::validate
* @see app/Http/Controllers/Api/DomainController.php:24
* @route '/api/domains/validate'
*/
validateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DomainController::validate
* @see app/Http/Controllers/Api/DomainController.php:24
* @route '/api/domains/validate'
*/
validateForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validate.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

validate.form = validateForm

/**
* @see \App\Http\Controllers\Api\DomainController::index
* @see app/Http/Controllers/Api/DomainController.php:38
* @route '/api/domains'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/domains',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\DomainController::index
* @see app/Http/Controllers/Api/DomainController.php:38
* @route '/api/domains'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DomainController::index
* @see app/Http/Controllers/Api/DomainController.php:38
* @route '/api/domains'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DomainController::index
* @see app/Http/Controllers/Api/DomainController.php:38
* @route '/api/domains'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\DomainController::index
* @see app/Http/Controllers/Api/DomainController.php:38
* @route '/api/domains'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DomainController::index
* @see app/Http/Controllers/Api/DomainController.php:38
* @route '/api/domains'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\DomainController::index
* @see app/Http/Controllers/Api/DomainController.php:38
* @route '/api/domains'
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
* @see \App\Http\Controllers\Api\DomainController::store
* @see app/Http/Controllers/Api/DomainController.php:47
* @route '/api/domains'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/domains',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\DomainController::store
* @see app/Http/Controllers/Api/DomainController.php:47
* @route '/api/domains'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\DomainController::store
* @see app/Http/Controllers/Api/DomainController.php:47
* @route '/api/domains'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\DomainController::store
* @see app/Http/Controllers/Api/DomainController.php:47
* @route '/api/domains'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\DomainController::store
* @see app/Http/Controllers/Api/DomainController.php:47
* @route '/api/domains'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\DomainController::update
* @see app/Http/Controllers/Api/DomainController.php:59
* @route '/api/domains/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/domains/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\DomainController::update
* @see app/Http/Controllers/Api/DomainController.php:59
* @route '/api/domains/{id}'
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
* @see \App\Http\Controllers\Api\DomainController::update
* @see app/Http/Controllers/Api/DomainController.php:59
* @route '/api/domains/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\DomainController::update
* @see app/Http/Controllers/Api/DomainController.php:59
* @route '/api/domains/{id}'
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
* @see \App\Http\Controllers\Api\DomainController::update
* @see app/Http/Controllers/Api/DomainController.php:59
* @route '/api/domains/{id}'
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
* @see \App\Http\Controllers\Api\DomainController::destroy
* @see app/Http/Controllers/Api/DomainController.php:71
* @route '/api/domains/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/domains/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\DomainController::destroy
* @see app/Http/Controllers/Api/DomainController.php:71
* @route '/api/domains/{id}'
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
* @see \App\Http\Controllers\Api\DomainController::destroy
* @see app/Http/Controllers/Api/DomainController.php:71
* @route '/api/domains/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\DomainController::destroy
* @see app/Http/Controllers/Api/DomainController.php:71
* @route '/api/domains/{id}'
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
* @see \App\Http\Controllers\Api\DomainController::destroy
* @see app/Http/Controllers/Api/DomainController.php:71
* @route '/api/domains/{id}'
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

const DomainController = { validate, index, store, update, destroy }

export default DomainController