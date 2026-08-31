import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\ToolController::index
* @see app/Http/Controllers/Site/ToolController.php:45
* @route '//lua.test/tools'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//lua.test/tools',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\ToolController::index
* @see app/Http/Controllers/Site/ToolController.php:45
* @route '//lua.test/tools'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\ToolController::index
* @see app/Http/Controllers/Site/ToolController.php:45
* @route '//lua.test/tools'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::index
* @see app/Http/Controllers/Site/ToolController.php:45
* @route '//lua.test/tools'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\ToolController::index
* @see app/Http/Controllers/Site/ToolController.php:45
* @route '//lua.test/tools'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::index
* @see app/Http/Controllers/Site/ToolController.php:45
* @route '//lua.test/tools'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::index
* @see app/Http/Controllers/Site/ToolController.php:45
* @route '//lua.test/tools'
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
* @see \App\Http\Controllers\Site\ToolController::utmBuilder
* @see app/Http/Controllers/Site/ToolController.php:58
* @route '//lua.test/tools/utm-builder'
*/
export const utmBuilder = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: utmBuilder.url(options),
    method: 'get',
})

utmBuilder.definition = {
    methods: ["get","head"],
    url: '//lua.test/tools/utm-builder',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\ToolController::utmBuilder
* @see app/Http/Controllers/Site/ToolController.php:58
* @route '//lua.test/tools/utm-builder'
*/
utmBuilder.url = (options?: RouteQueryOptions) => {
    return utmBuilder.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\ToolController::utmBuilder
* @see app/Http/Controllers/Site/ToolController.php:58
* @route '//lua.test/tools/utm-builder'
*/
utmBuilder.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: utmBuilder.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::utmBuilder
* @see app/Http/Controllers/Site/ToolController.php:58
* @route '//lua.test/tools/utm-builder'
*/
utmBuilder.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: utmBuilder.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\ToolController::utmBuilder
* @see app/Http/Controllers/Site/ToolController.php:58
* @route '//lua.test/tools/utm-builder'
*/
const utmBuilderForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: utmBuilder.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::utmBuilder
* @see app/Http/Controllers/Site/ToolController.php:58
* @route '//lua.test/tools/utm-builder'
*/
utmBuilderForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: utmBuilder.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::utmBuilder
* @see app/Http/Controllers/Site/ToolController.php:58
* @route '//lua.test/tools/utm-builder'
*/
utmBuilderForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: utmBuilder.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

utmBuilder.form = utmBuilderForm

/**
* @see \App\Http\Controllers\Site\ToolController::qrGenerator
* @see app/Http/Controllers/Site/ToolController.php:68
* @route '//lua.test/tools/qr-generator'
*/
export const qrGenerator = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qrGenerator.url(options),
    method: 'get',
})

qrGenerator.definition = {
    methods: ["get","head"],
    url: '//lua.test/tools/qr-generator',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\ToolController::qrGenerator
* @see app/Http/Controllers/Site/ToolController.php:68
* @route '//lua.test/tools/qr-generator'
*/
qrGenerator.url = (options?: RouteQueryOptions) => {
    return qrGenerator.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\ToolController::qrGenerator
* @see app/Http/Controllers/Site/ToolController.php:68
* @route '//lua.test/tools/qr-generator'
*/
qrGenerator.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qrGenerator.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::qrGenerator
* @see app/Http/Controllers/Site/ToolController.php:68
* @route '//lua.test/tools/qr-generator'
*/
qrGenerator.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: qrGenerator.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\ToolController::qrGenerator
* @see app/Http/Controllers/Site/ToolController.php:68
* @route '//lua.test/tools/qr-generator'
*/
const qrGeneratorForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qrGenerator.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::qrGenerator
* @see app/Http/Controllers/Site/ToolController.php:68
* @route '//lua.test/tools/qr-generator'
*/
qrGeneratorForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qrGenerator.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::qrGenerator
* @see app/Http/Controllers/Site/ToolController.php:68
* @route '//lua.test/tools/qr-generator'
*/
qrGeneratorForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: qrGenerator.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

qrGenerator.form = qrGeneratorForm

/**
* @see \App\Http\Controllers\Site\ToolController::linkChecker
* @see app/Http/Controllers/Site/ToolController.php:78
* @route '//lua.test/tools/link-checker'
*/
export const linkChecker = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: linkChecker.url(options),
    method: 'get',
})

linkChecker.definition = {
    methods: ["get","head"],
    url: '//lua.test/tools/link-checker',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\ToolController::linkChecker
* @see app/Http/Controllers/Site/ToolController.php:78
* @route '//lua.test/tools/link-checker'
*/
linkChecker.url = (options?: RouteQueryOptions) => {
    return linkChecker.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\ToolController::linkChecker
* @see app/Http/Controllers/Site/ToolController.php:78
* @route '//lua.test/tools/link-checker'
*/
linkChecker.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: linkChecker.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::linkChecker
* @see app/Http/Controllers/Site/ToolController.php:78
* @route '//lua.test/tools/link-checker'
*/
linkChecker.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: linkChecker.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\ToolController::linkChecker
* @see app/Http/Controllers/Site/ToolController.php:78
* @route '//lua.test/tools/link-checker'
*/
const linkCheckerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: linkChecker.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::linkChecker
* @see app/Http/Controllers/Site/ToolController.php:78
* @route '//lua.test/tools/link-checker'
*/
linkCheckerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: linkChecker.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ToolController::linkChecker
* @see app/Http/Controllers/Site/ToolController.php:78
* @route '//lua.test/tools/link-checker'
*/
linkCheckerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: linkChecker.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

linkChecker.form = linkCheckerForm

/**
* @see \App\Http\Controllers\Site\ToolController::check
* @see app/Http/Controllers/Site/ToolController.php:92
* @route '//lua.test/tools/link-checker'
*/
export const check = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: check.url(options),
    method: 'post',
})

check.definition = {
    methods: ["post"],
    url: '//lua.test/tools/link-checker',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Site\ToolController::check
* @see app/Http/Controllers/Site/ToolController.php:92
* @route '//lua.test/tools/link-checker'
*/
check.url = (options?: RouteQueryOptions) => {
    return check.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\ToolController::check
* @see app/Http/Controllers/Site/ToolController.php:92
* @route '//lua.test/tools/link-checker'
*/
check.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: check.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Site\ToolController::check
* @see app/Http/Controllers/Site/ToolController.php:92
* @route '//lua.test/tools/link-checker'
*/
const checkForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: check.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Site\ToolController::check
* @see app/Http/Controllers/Site/ToolController.php:92
* @route '//lua.test/tools/link-checker'
*/
checkForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: check.url(options),
    method: 'post',
})

check.form = checkForm

const ToolController = { index, utmBuilder, qrGenerator, linkChecker, check }

export default ToolController