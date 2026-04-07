import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:17
* @route '/verify-email'
*/
const EmailVerificationPromptController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EmailVerificationPromptController.url(options),
    method: 'get',
})

EmailVerificationPromptController.definition = {
    methods: ["get","head"],
    url: '/verify-email',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:17
* @route '/verify-email'
*/
EmailVerificationPromptController.url = (options?: RouteQueryOptions) => {
    return EmailVerificationPromptController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:17
* @route '/verify-email'
*/
EmailVerificationPromptController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EmailVerificationPromptController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:17
* @route '/verify-email'
*/
EmailVerificationPromptController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EmailVerificationPromptController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:17
* @route '/verify-email'
*/
const EmailVerificationPromptControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EmailVerificationPromptController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:17
* @route '/verify-email'
*/
EmailVerificationPromptControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EmailVerificationPromptController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:17
* @route '/verify-email'
*/
EmailVerificationPromptControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EmailVerificationPromptController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

EmailVerificationPromptController.form = EmailVerificationPromptControllerForm

export default EmailVerificationPromptController