import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::show
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/stripe/payment/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::show
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::show
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::show
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::show
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::show
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Cashier\Http\Controllers\PaymentController::show
* @see vendor/laravel/cashier/src/Http/Controllers/PaymentController.php:30
* @route '/stripe/payment/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const PaymentController = { show }

export default PaymentController