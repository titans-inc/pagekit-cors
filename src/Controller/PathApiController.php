<?php

namespace TitansInc\CORS\Controller;

use Pagekit\Application as App;
use TitansInc\CORS\Model\Path;

/**
 * @Access("system: access settings")
 * @Route("path", name="path")
 */
class PathApiController {

    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = 0) {
        $query  = Path::query();
        $filter = array_merge(array_fill_keys(['search', 'order', 'limit'], ''), $filter);

        extract($filter, EXTR_SKIP);

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['path LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if (!preg_match('/^(id)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'id', 2 => 'desc'];
        }

        $limit = (int) $limit ?: 15;
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $paths = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->get());

        return compact('paths', 'pages', 'count');
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id) {
        return Path::where(compact('id'))->first();
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"path": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0) {
        if (!$id || !$path = Path::find($id)) {

            if ($id) {
                App::abort(404, __('Path not found.'));
            }
        }
        
        $path = Path::create($data);
        $path->save();

        return ['message' => 'success', 'path' => $path];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"paths": "array"}, csrf=true)
     */
    public function bulkSaveAction($paths = []) {
        foreach ($paths as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($path = Path::find($id)) {
            $path->delete();
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "array"}, csrf=true)
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return ['message' => 'success'];
    }
}