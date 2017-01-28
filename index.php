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
    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(new \TitansInc\CORS\Listener\CORSListener($app));
        }
    ]
];
