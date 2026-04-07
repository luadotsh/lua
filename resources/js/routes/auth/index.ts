import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import invites from './invites'
import google723582 from './google'
/**
* @see \App\Http\Controllers\Auth\GoogleController::google
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
export const google = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: google.url(options),
    method: 'get',
})

google.definition = {
    methods: ["get","head"],
    url: '/google/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\GoogleController::google
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
google.url = (options?: RouteQueryOptions) => {
    return google.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleController::google
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
google.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: google.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::google
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
google.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: google.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::google
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
const googleForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: google.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::google
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
googleForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: google.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleController::google
* @see app/Http/Controllers/Auth/GoogleController.php:21
* @route '/google/login'
*/
googleForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: google.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

google.form = googleForm

const auth = {
    invites: Object.assign(invites, invites),
    google: Object.assign(google, google723582),
}

export default auth