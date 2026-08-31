<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Social Authentication
    |--------------------------------------------------------------------------
    |
    | Whether "Continue with Google" and "Continue with GitHub" are offered on
    | the login and register pages.
    |
    | A provider needs both: the switch here, and credentials in services.php.
    | The credential check is what keeps a self-hosted install from rendering a
    | button that leads straight to an OAuth error, and the switch is what lets
    | you turn a provider off without deleting the credentials to do it.
    |
    | Default true, so configuring credentials is enough to get the button.
    |
    */

    'auth' => [
        'google' => (bool) env('GOOGLE_AUTH_ENABLED', true),
        'github' => (bool) env('GITHUB_AUTH_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | How many rows a list asks for. The page size never comes from the
    | request: a caller cannot widen it, and every list action reads this.
    |
    | The public REST API is the exception — it has its own fixed, documented
    | page size, because changing this must not change an API contract.
    |
    */

    'pagination' => [
        'default' => (int) env('LUA_PAGINATION_DEFAULT', 20),
    ],

];
