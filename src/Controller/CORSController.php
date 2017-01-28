<?php

namespace TitansInc\CORS\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class CORSController {
    public function pathsAction() {
        return [
            '$view' => [
                'title' => 'Cors',
                'name' => 'cors:views/admin/paths.twig'
            ],
        ];
    }

    public function settingsAction() {
        $module = App::module('pagekit-cors');

        return [
            '$view' => [
                'title' => 'Cors',
                'name' => 'cors:views/admin/settings.twig'
            ],
            '$data' => ['cors' => $module->config],
            'arr' => [
                'Allow Origins' => 'allow_origin',
                'Allow Headers' => 'allow_headers',
                'Allow Methods' => 'allow_methods',
                'Expose Headers' => 'expose_headers',
                'Hosts' => 'hosts'
            ]
        ];
    }

    /**
     * @Route(methods="POST")
     * @Request({"cors": "array"}, csrf=true)
     */
    public function saveAction($cors = []) {
        $ocors = App::config('pagekit-cors');
        
        foreach ($cors as $key => $value) {
            $ocors->set($key, $value);
        }
        
        return ['message' => 'success'];
    }
}