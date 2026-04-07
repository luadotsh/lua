import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::payment
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
export const payment = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: payment.url(args, options),
    method: 'get',
})

payment.definition = {
    methods: ["get","head"],
    url: '/stripe/payment/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::payment
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
payment.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return payment.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::payment
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
payment.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: payment.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::payment
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
payment.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: payment.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::payment
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
const paymentForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: payment.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::payment
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
paymentForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: payment.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::payment
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
paymentForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: payment.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

payment.form = paymentForm

/**
* @see \Laravel\Cashier\Http\Controllers\WebhookController::webhook
* @see vendor/laravel/cashier/src/Http/Controllers/WebhookController.php:40
* @route '/stripe/webhook'
*/
export const webhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

webhook.definition = {
    methods: ["post"],
    url: '/stripe/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Cashier\Http\Controllers\WebhookController::webhook
* @see vendor/laravel/cashier/src/Http/Controllers/WebhookController.php:40
* @route '/stripe/webhook'
*/
webhook.url = (options?: RouteQueryOptions) => {
    return webhook.definition.url + queryParams(options)
}

/**
* @see \Laravel\Cashier\Http\Controllers\WebhookController::webhook
* @see vendor/laravel/cashier/src/Http/Controllers/WebhookController.php:40
* @route '/stripe/webhook'
*/
webhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

/**
* @see \Laravel\Cashier\Http\Controllers\WebhookController::webhook
* @see vendor/laravel/cashier/src/Http/Controllers/WebhookController.php:40
* @route '/stripe/webhook'
*/
const webhookForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

/**
* @see \Laravel\Cashier\Http\Controllers\WebhookController::webhook
* @see vendor/laravel/cashier/src/Http/Controllers/WebhookController.php:40
* @route '/stripe/webhook'
*/
webhookForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: webhook.url(options),
    method: 'post',
})

webhook.form = webhookForm

const cashier = {
    payment: Object.assign(payment, payment),
    webhook: Object.assign(webhook, webhook),
}

export default cashier