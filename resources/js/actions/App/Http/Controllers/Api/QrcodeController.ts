import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
const QrcodeController = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: QrcodeController.url(args, options),
    method: 'get',
})

QrcodeController.definition = {
    methods: ["get","head"],
    url: '/api/v1/links/{id}/qr-code',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
QrcodeController.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return QrcodeController.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
QrcodeController.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: QrcodeController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
QrcodeController.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: QrcodeController.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
const QrcodeControllerForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: QrcodeController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
QrcodeControllerForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: QrcodeController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\QrcodeController::__invoke
* @see app/Http/Controllers/Api/QrcodeController.php:19
* @route '/api/v1/links/{id}/qr-code'
*/
QrcodeControllerForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: QrcodeController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

QrcodeController.form = QrcodeControllerForm

export default QrcodeController