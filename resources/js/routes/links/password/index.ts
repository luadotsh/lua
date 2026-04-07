import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\RedirectController::validate
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
export const validate = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: validate.url(args, options),
    method: 'post',
})

validate.definition = {
    methods: ["post"],
    url: '/{key}/password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RedirectController::validate
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
validate.url = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { key: args }
    }

    if (Array.isArray(args)) {
        args = {
            key: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        key: args.key,
    }

    return validate.definition.url
            .replace('{key}', parsedArgs.key.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RedirectController::validate
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
validate.post = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: validate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RedirectController::validate
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
const validateForm = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: validate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RedirectController::validate
* @see app/Http/Controllers/RedirectController.php:100
* @route '/{key}/password'
*/
validateForm.post = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: validate.url(args, options),
    method: 'post',
})

validate.form = validateForm

const password = {
    validate: Object.assign(validate, validate),
}

export default password