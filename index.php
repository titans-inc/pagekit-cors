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
        ]
    ],

    'menu' => [
        'cors' => [
            'label' => 'CORS',
            'icon'  => 'cors:icon.svg',
            'url' => '@cors/paths',
            'active' => '@cors/paths*',
            'access' => 'system: access settings',
            'priority' => 110
        ],
        'cors: paths' => [
            'label' => 'Paths',
            'parent' => 'cors',
            'url' => '@cors/paths',
            'active' => '@cors/paths*',
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

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(new \TitansInc\CORS\Listener\CORSListener($app));
        }
    ]
];
