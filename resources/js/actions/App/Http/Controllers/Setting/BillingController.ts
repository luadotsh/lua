import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Setting\BillingController::index
* @see app/Http/Controllers/Setting/BillingController.php:16
* @route '/settings/billing'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/billing',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\BillingController::index
* @see app/Http/Controllers/Setting/BillingController.php:16
* @route '/settings/billing'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\BillingController::index
* @see app/Http/Controllers/Setting/BillingController.php:16
* @route '/settings/billing'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::index
* @see app/Http/Controllers/Setting/BillingController.php:16
* @route '/settings/billing'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::index
* @see app/Http/Controllers/Setting/BillingController.php:16
* @route '/settings/billing'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::index
* @see app/Http/Controllers/Setting/BillingController.php:16
* @route '/settings/billing'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::index
* @see app/Http/Controllers/Setting/BillingController.php:16
* @route '/settings/billing'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Setting\BillingController::upgrade
* @see app/Http/Controllers/Setting/BillingController.php:21
* @route '/settings/billing/upgrade'
*/
export const upgrade = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upgrade.url(options),
    method: 'get',
})

upgrade.definition = {
    methods: ["get","head"],
    url: '/settings/billing/upgrade',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\BillingController::upgrade
* @see app/Http/Controllers/Setting/BillingController.php:21
* @route '/settings/billing/upgrade'
*/
upgrade.url = (options?: RouteQueryOptions) => {
    return upgrade.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\BillingController::upgrade
* @see app/Http/Controllers/Setting/BillingController.php:21
* @route '/settings/billing/upgrade'
*/
upgrade.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upgrade.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::upgrade
* @see app/Http/Controllers/Setting/BillingController.php:21
* @route '/settings/billing/upgrade'
*/
upgrade.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: upgrade.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::upgrade
* @see app/Http/Controllers/Setting/BillingController.php:21
* @route '/settings/billing/upgrade'
*/
const upgradeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upgrade.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::upgrade
* @see app/Http/Controllers/Setting/BillingController.php:21
* @route '/settings/billing/upgrade'
*/
upgradeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upgrade.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::upgrade
* @see app/Http/Controllers/Setting/BillingController.php:21
* @route '/settings/billing/upgrade'
*/
upgradeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upgrade.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

upgrade.form = upgradeForm

/**
* @see \App\Http\Controllers\Setting\BillingController::checkout
* @see app/Http/Controllers/Setting/BillingController.php:29
* @route '/settings/billing/checkout/{planId}'
*/
export const checkout = (args: { planId: string | number } | [planId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(args, options),
    method: 'get',
})

checkout.definition = {
    methods: ["get","head"],
    url: '/settings/billing/checkout/{planId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\BillingController::checkout
* @see app/Http/Controllers/Setting/BillingController.php:29
* @route '/settings/billing/checkout/{planId}'
*/
checkout.url = (args: { planId: string | number } | [planId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { planId: args }
    }

    if (Array.isArray(args)) {
        args = {
            planId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        planId: args.planId,
    }

    return checkout.definition.url
            .replace('{planId}', parsedArgs.planId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\BillingController::checkout
* @see app/Http/Controllers/Setting/BillingController.php:29
* @route '/settings/billing/checkout/{planId}'
*/
checkout.get = (args: { planId: string | number } | [planId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::checkout
* @see app/Http/Controllers/Setting/BillingController.php:29
* @route '/settings/billing/checkout/{planId}'
*/
checkout.head = (args: { planId: string | number } | [planId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkout.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::checkout
* @see app/Http/Controllers/Setting/BillingController.php:29
* @route '/settings/billing/checkout/{planId}'
*/
const checkoutForm = (args: { planId: string | number } | [planId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::checkout
* @see app/Http/Controllers/Setting/BillingController.php:29
* @route '/settings/billing/checkout/{planId}'
*/
checkoutForm.get = (args: { planId: string | number } | [planId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::checkout
* @see app/Http/Controllers/Setting/BillingController.php:29
* @route '/settings/billing/checkout/{planId}'
*/
checkoutForm.head = (args: { planId: string | number } | [planId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkout.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

checkout.form = checkoutForm

/**
* @see \App\Http\Controllers\Setting\BillingController::billingPortal
* @see app/Http/Controllers/Setting/BillingController.php:48
* @route '/settings/billing/portal'
*/
export const billingPortal = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: billingPortal.url(options),
    method: 'get',
})

billingPortal.definition = {
    methods: ["get","head"],
    url: '/settings/billing/portal',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\BillingController::billingPortal
* @see app/Http/Controllers/Setting/BillingController.php:48
* @route '/settings/billing/portal'
*/
billingPortal.url = (options?: RouteQueryOptions) => {
    return billingPortal.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\BillingController::billingPortal
* @see app/Http/Controllers/Setting/BillingController.php:48
* @route '/settings/billing/portal'
*/
billingPortal.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: billingPortal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::billingPortal
* @see app/Http/Controllers/Setting/BillingController.php:48
* @route '/settings/billing/portal'
*/
billingPortal.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: billingPortal.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::billingPortal
* @see app/Http/Controllers/Setting/BillingController.php:48
* @route '/settings/billing/portal'
*/
const billingPortalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: billingPortal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::billingPortal
* @see app/Http/Controllers/Setting/BillingController.php:48
* @route '/settings/billing/portal'
*/
billingPortalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: billingPortal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::billingPortal
* @see app/Http/Controllers/Setting/BillingController.php:48
* @route '/settings/billing/portal'
*/
billingPortalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: billingPortal.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

billingPortal.form = billingPortalForm

/**
* @see \App\Http\Controllers\Setting\BillingController::swapFreePlan
* @see app/Http/Controllers/Setting/BillingController.php:0
* @route '/settings/billing/swap-free-plan'
*/
export const swapFreePlan = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: swapFreePlan.url(options),
    method: 'get',
})

swapFreePlan.definition = {
    methods: ["get","head"],
    url: '/settings/billing/swap-free-plan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Setting\BillingController::swapFreePlan
* @see app/Http/Controllers/Setting/BillingController.php:0
* @route '/settings/billing/swap-free-plan'
*/
swapFreePlan.url = (options?: RouteQueryOptions) => {
    return swapFreePlan.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Setting\BillingController::swapFreePlan
* @see app/Http/Controllers/Setting/BillingController.php:0
* @route '/settings/billing/swap-free-plan'
*/
swapFreePlan.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: swapFreePlan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::swapFreePlan
* @see app/Http/Controllers/Setting/BillingController.php:0
* @route '/settings/billing/swap-free-plan'
*/
swapFreePlan.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: swapFreePlan.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::swapFreePlan
* @see app/Http/Controllers/Setting/BillingController.php:0
* @route '/settings/billing/swap-free-plan'
*/
const swapFreePlanForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: swapFreePlan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::swapFreePlan
* @see app/Http/Controllers/Setting/BillingController.php:0
* @route '/settings/billing/swap-free-plan'
*/
swapFreePlanForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: swapFreePlan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Setting\BillingController::swapFreePlan
* @see app/Http/Controllers/Setting/BillingController.php:0
* @route '/settings/billing/swap-free-plan'
*/
swapFreePlanForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: swapFreePlan.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

swapFreePlan.form = swapFreePlanForm

const BillingController = { index, upgrade, checkout, billingPortal, swapFreePlan }

export default BillingController