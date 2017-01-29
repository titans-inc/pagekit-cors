<?php

use Pagekit\Application as App;

return [
    'name' => 'pagekit-cors',

    'type' => 'extension',

    'resources' => [
        'cors:' => ''
    ],

    'autoload' => [
        'TitansInc\\CORS\\' => 'src'
    ],

    'config' => [
        'allow_credentials' => false,
        'allow_origin' => [],
        'allow_headers' => [],
        'allow_methods' => [],
        'expose_headers' => [],
        'max_age' => 0,
        'hosts' => [],
        'origin_regex' => false,
        'forced_allow_origin_value' => ''
    ],

    'routes' => [
        '/cors' => [
            'name' => '@cors',
            'controller' => 'TitansInc\\CORS\\Controller\\CORSController'
        ],
        '/api/cors' => [
            'name' => '@cors/api',
            'controller' => [
                'TitansInc\\CORS\\Controller\\PathApiController',
            ]
        ]
    ],

    'menu' => [
        'cors' => [
            'label' => 'CORS',
            'icon'  => 'cors:icon.svg',
            'url' => '@cors/path',
            'active' => '@cors/path*',
            'access' => 'system: access settings',
            'priority' => 110
        ],
        'cors: paths' => [
            'label' => 'Paths',
            'parent' => 'cors',
            'url' => '@cors/path',
            'active' => '@cors/path*',
            'access' => 'system: access settings'
        ],
        'cors: settings' => [
            'label' => 'Settings',
            'parent' => 'cors',
            'url' => '@cors/settings',
            'active' => '@cors/settings*',
            'access' => 'system: access settings'
        ]
    ],

    'settings' => '@cors/settings',

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(new \TitansInc\CORS\Listener\CORSListener($app));
        }
    ]
];
