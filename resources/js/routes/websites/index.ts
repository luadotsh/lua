import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
export const favicon = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: favicon.url(options),
    method: 'get',
})

favicon.definition = {
    methods: ["get","head"],
    url: '/api/v1/websites/favicon',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
favicon.url = (options?: RouteQueryOptions) => {
    return favicon.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
favicon.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: favicon.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
favicon.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: favicon.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
const faviconForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: favicon.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
faviconForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: favicon.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WebsiteController::__invoke
* @see app/Http/Controllers/Api/WebsiteController.php:11
* @route '/api/v1/websites/favicon'
*/
faviconForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: favicon.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

favicon.form = faviconForm

const websites = {
    favicon: Object.assign(favicon, favicon),
}

export default websites