import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
const WebsiteController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: WebsiteController.url(options),
    method: 'get',
})

WebsiteController.definition = {
    methods: ["get","head"],
    url: '/api/v1/websites/favicon',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
WebsiteController.url = (options?: RouteQueryOptions) => {
    return WebsiteController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
WebsiteController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: WebsiteController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
WebsiteController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: WebsiteController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
const WebsiteControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: WebsiteController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
WebsiteControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: WebsiteController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
WebsiteControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: WebsiteController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

WebsiteController.form = WebsiteControllerForm

export default WebsiteController