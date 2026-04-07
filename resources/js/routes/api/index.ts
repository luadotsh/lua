import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import links from './links'
import tags from './tags'
import domains from './domains'
/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
export const qrCode = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qrCode.url(args, options),
    method: 'get',
})

qrCode.definition = {
    methods: ["get","head"],
    url: '/api/v1/links/{id}/qr-code',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
qrCode.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return qrCode.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
qrCode.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qrCode.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
qrCode.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: qrCode.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
const qrCodeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qrCode.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
qrCodeForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qrCode.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
qrCodeForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qrCode.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

qrCode.form = qrCodeForm

const api = {
    links: Object.assign(links, links),
    qrCode: Object.assign(qrCode, qrCode),
    tags: Object.assign(tags, tags),
    domains: Object.assign(domains, domains),
}

export default api