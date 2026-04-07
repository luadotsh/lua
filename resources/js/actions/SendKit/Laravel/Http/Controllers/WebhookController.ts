import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
const WebhookController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: WebhookController.url(options),
    method: 'post',
})

WebhookController.definition = {
    methods: ["post"],
    url: '/webhook/sendkit',
} satisfies RouteDefinition<["post"]>

/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
WebhookController.url = (options?: RouteQueryOptions) => {
    return WebhookController.definition.url + queryParams(options)
}

/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
WebhookController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: WebhookController.url(options),
    method: 'post',
})

/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
const WebhookControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: WebhookController.url(options),
    method: 'post',
})

/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
WebhookControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: WebhookController.url(options),
    method: 'post',
})

WebhookController.form = WebhookControllerForm

export default WebhookController