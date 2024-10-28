<?php

declare(strict_types=1);

return [

    /*
    | The list of reserved subdomains for the application.
    | These subdomains are not allowed to be used by users.
    */

    'main' => env('DOMAIN_MAIN', 'lua.sh'),

    'cname' => env('DOMAIN_CNAME', 'cname.lua.sh'),

    'available' => [
        env('DOMAIN_MAIN', 'lua.sh'),
        'git.now',
        'cal.now',
        'fig.now',
        'spoti.now'
    ]
];
