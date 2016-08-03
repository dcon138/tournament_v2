<?php

namespace Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Psy\Exception\FatalErrorException;
use Illuminate\Database\Eloquent\Model;

abstract class RestResourceController extends BaseController {
    protected $modelClass;
    protected $requestClass;

    /**
     * RestResourceController constructor. Checks that modelClass for the resource has been defined correctly.
     *
     * This constructor should be called as the last line of the constructor for any child controller(s).
     */
    public function __construct()
    {
        if (empty($this->modelClass)) {
            throw new FatalErrorException('Child Model class must be defined in Child Controller');
        } else if (!is_subclass_of($this->modelClass, Model::class)) {
            throw new FatalErrorException('Child Model provided does not extend base Eloquent model class');
        }
    }

    /**
     * Create a new client group entity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $this->validateChildFormRequest($request);
            $model = $this->modelClass;
            $entity = $model::create($request->input());
            return response()->json($entity->toArray());
        } catch (FatalErrorException $e) {
            return response()->json(['error' => 'An internal error has occurred'], 500);
        }
    }

    /**
     * Validates the request according to validation and authorization rules defined in a child class of FormRequest.
     * This is necessary because we can't type-hint based on dynamic values set in child classes.
     *
     * @param $request - the current request object
     * @throws FatalErrorException - if the child class of FormRequest hasn't been set into $this->requestClass in the
     *                               child controller.
     */
    private function validateChildFormRequest($request)
    {
        if (empty($this->requestClass)) {
            throw new FatalErrorException('Child FormRequest class must be defined with validation and authorize rules');
        }
        $childRequestClass = $this->requestClass;
        $newRequest = $childRequestClass::createFromBase($request);
        $newRequest->setContainer(Container::getInstance());
        $newRequest->validate();
    }
}