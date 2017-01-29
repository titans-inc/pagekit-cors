<?php

namespace TitansInc\CORS\Controller;

use Pagekit\Application as App;
use TitansInc\CORS\Model\Path;

/**
 * @Access(admin=true)
 */
class CORSController {

    /**
     * @Access("system: access settings")
     * @Request({"filter": "array", "page":"int"})
     */
    public function pathAction($filter = null, $page = null) {
        return [
            '$view' => [
                'title' => 'Cors',
                'name' => 'cors:views/admin/paths.twig'
            ],
            '$data' => [
                'config'   => [
                    'filter' => (object) $filter,
                    'page'   => $page
                ]
            ]
        ];
    }

    /**
     * @Access("system: access settings")
     */
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

    /**
     * @Route("/path/edit", name="path/edit")
     * @Access("system: access settings")
     * @Request({"id": "int"})
     */
    public function editAction($id = 0) {
        try {

            if (!$path = Path::where(compact('id'))->first()) {

                if ($id) {
                    App::abort(404, __('Invalid path id.'));
                }

                $path = Path::create();
            }

            return [
                '$view' => [
                    'title' => $id ? __('Edit Path') : __('Add Path'),
                    'name'  => 'cors:views/admin/path-edit.twig'
                ],
                '$data' => [
                    'path' => $path,
                ],
                'path' => $path,
                'arr' => [
                    'Allow Origins' => 'allow_origin',
                    'Allow Headers' => 'allow_headers',
                    'Allow Methods' => 'allow_methods',
                    'Expose Headers' => 'expose_headers',
                    'Hosts' => 'hosts'
                ]
            ];

        } catch (\Exception $e) {

            App::message()->error($e->getMessage());

            return App::redirect('@blog/post');
        }
    }
    
}