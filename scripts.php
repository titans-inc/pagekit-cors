<?php

return [

    'install' => function ($app) {
        $util = $app['db']->getUtility();
    },

    'uninstall' => function ($app) {
        $util = $app['db']->getUtility();
    },

    'updates' => [
    ]

];