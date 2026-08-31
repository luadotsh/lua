import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import blog from './blog'
import useCases from './use-cases'
import glossary from './glossary'
import tools from './tools'
import alternatives from './alternatives'
/**
* @see \App\Http\Controllers\Site\PageController::home
* @see app/Http/Controllers/Site/PageController.php:14
* @route '//lua.test'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '//lua.test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::home
* @see app/Http/Controllers/Site/PageController.php:14
* @route '//lua.test'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::home
* @see app/Http/Controllers/Site/PageController.php:14
* @route '//lua.test'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::home
* @see app/Http/Controllers/Site/PageController.php:14
* @route '//lua.test'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\PageController::home
* @see app/Http/Controllers/Site/PageController.php:14
* @route '//lua.test'
*/
const homeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::home
* @see app/Http/Controllers/Site/PageController.php:14
* @route '//lua.test'
*/
homeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::home
* @see app/Http/Controllers/Site/PageController.php:14
* @route '//lua.test'
*/
homeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

home.form = homeForm

/**
* @see \App\Http\Controllers\Site\PageController::pricing
* @see app/Http/Controllers/Site/PageController.php:38
* @route '//lua.test/pricing'
*/
export const pricing = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pricing.url(options),
    method: 'get',
})

pricing.definition = {
    methods: ["get","head"],
    url: '//lua.test/pricing',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::pricing
* @see app/Http/Controllers/Site/PageController.php:38
* @route '//lua.test/pricing'
*/
pricing.url = (options?: RouteQueryOptions) => {
    return pricing.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::pricing
* @see app/Http/Controllers/Site/PageController.php:38
* @route '//lua.test/pricing'
*/
pricing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pricing.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::pricing
* @see app/Http/Controllers/Site/PageController.php:38
* @route '//lua.test/pricing'
*/
pricing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pricing.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\PageController::pricing
* @see app/Http/Controllers/Site/PageController.php:38
* @route '//lua.test/pricing'
*/
const pricingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pricing.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::pricing
* @see app/Http/Controllers/Site/PageController.php:38
* @route '//lua.test/pricing'
*/
pricingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pricing.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::pricing
* @see app/Http/Controllers/Site/PageController.php:38
* @route '//lua.test/pricing'
*/
pricingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pricing.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

pricing.form = pricingForm

/**
* @see \App\Http\Controllers\Site\PageController::faq
* @see app/Http/Controllers/Site/PageController.php:83
* @route '//lua.test/faq'
*/
export const faq = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: faq.url(options),
    method: 'get',
})

faq.definition = {
    methods: ["get","head"],
    url: '//lua.test/faq',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::faq
* @see app/Http/Controllers/Site/PageController.php:83
* @route '//lua.test/faq'
*/
faq.url = (options?: RouteQueryOptions) => {
    return faq.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::faq
* @see app/Http/Controllers/Site/PageController.php:83
* @route '//lua.test/faq'
*/
faq.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: faq.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::faq
* @see app/Http/Controllers/Site/PageController.php:83
* @route '//lua.test/faq'
*/
faq.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: faq.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\PageController::faq
* @see app/Http/Controllers/Site/PageController.php:83
* @route '//lua.test/faq'
*/
const faqForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::faq
* @see app/Http/Controllers/Site/PageController.php:83
* @route '//lua.test/faq'
*/
faqForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::faq
* @see app/Http/Controllers/Site/PageController.php:83
* @route '//lua.test/faq'
*/
faqForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

faq.form = faqForm

/**
* @see \App\Http\Controllers\Site\PageController::terms
* @see app/Http/Controllers/Site/PageController.php:116
* @route '//lua.test/terms'
*/
export const terms = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: terms.url(options),
    method: 'get',
})

terms.definition = {
    methods: ["get","head"],
    url: '//lua.test/terms',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::terms
* @see app/Http/Controllers/Site/PageController.php:116
* @route '//lua.test/terms'
*/
terms.url = (options?: RouteQueryOptions) => {
    return terms.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::terms
* @see app/Http/Controllers/Site/PageController.php:116
* @route '//lua.test/terms'
*/
terms.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: terms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::terms
* @see app/Http/Controllers/Site/PageController.php:116
* @route '//lua.test/terms'
*/
terms.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: terms.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\PageController::terms
* @see app/Http/Controllers/Site/PageController.php:116
* @route '//lua.test/terms'
*/
const termsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: terms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::terms
* @see app/Http/Controllers/Site/PageController.php:116
* @route '//lua.test/terms'
*/
termsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: terms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::terms
* @see app/Http/Controllers/Site/PageController.php:116
* @route '//lua.test/terms'
*/
termsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: terms.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

terms.form = termsForm

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:126
* @route '//lua.test/privacy'
*/
export const privacy = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: privacy.url(options),
    method: 'get',
})

privacy.definition = {
    methods: ["get","head"],
    url: '//lua.test/privacy',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:126
* @route '//lua.test/privacy'
*/
privacy.url = (options?: RouteQueryOptions) => {
    return privacy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:126
* @route '//lua.test/privacy'
*/
privacy.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: privacy.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:126
* @route '//lua.test/privacy'
*/
privacy.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: privacy.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:126
* @route '//lua.test/privacy'
*/
const privacyForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: privacy.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:126
* @route '//lua.test/privacy'
*/
privacyForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: privacy.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:126
* @route '//lua.test/privacy'
*/
privacyForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: privacy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

privacy.form = privacyForm

const site = {
    home: Object.assign(home, home),
    pricing: Object.assign(pricing, pricing),
    faq: Object.assign(faq, faq),
    terms: Object.assign(terms, terms),
    privacy: Object.assign(privacy, privacy),
    blog: Object.assign(blog, blog),
    useCases: Object.assign(useCases, useCases),
    glossary: Object.assign(glossary, glossary),
    tools: Object.assign(tools, tools),
    alternatives: Object.assign(alternatives, alternatives),
}

export default site