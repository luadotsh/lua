import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
export const webhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

webhook.definition = {
    methods: ["post"],
    url: '/webhook/sendkit',
} satisfies RouteDefinition<["post"]>

/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
webhook.url = (options?: RouteQueryOptions) => {
    return webhook.definition.url + queryParams(options)
}

/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
webhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
const webhookForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

/**
* @see \SendKit\Laravel\Http\Controllers\WebhookController::__invoke
* @see vendor/sendkit/sendkit-laravel/src/Http/Controllers/WebhookController.php:41
* @route '/webhook/sendkit'
*/
webhookForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

webhook.form = webhookForm

const sendkit = {
    webhook: Object.assign(webhook, webhook),
}

export default sendkit