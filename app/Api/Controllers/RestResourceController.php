<?php

namespace Api\Controllers;

abstract class RestResourceController extends BaseController {
    protected $modelClass;
    protected $requestClass;

    /**
     * Create a new client group entity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) //TODO we need to figure out a way to run the child controllers FormRequest object from the parent.
    {
        try {
            $model = $this->modelClass;
            $entity = $model::create($request->input());
            return response()->json($entity->toArray());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}