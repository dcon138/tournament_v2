<?php

namespace Api\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Psy\Exception\FatalErrorException;
use App\BaseModel;
use Illuminate\Support\Facades\Route;
use Dingo\Api\Http\Response;

abstract class RestResourceController extends BaseController {
    protected $modelClass;
    protected $requestClasses;

    /**
     * RestResourceController constructor. Checks that modelClass for the resource has been defined correctly.
     *
     * This constructor should be called as the last line of the constructor for any child controller(s).
     */
    public function __construct()
    {
        if (empty($this->modelClass)) {
            throw new FatalErrorException('Child Model class must be defined in Child Controller');
        } else if (!is_subclass_of($this->modelClass, BaseModel::class)) {
            throw new FatalErrorException('Child Model provided does not extend base model class');
        }
    }

    /**
     * Create a new entity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $this->validateChildFormRequest($request, 'create');
            $model = $this->modelClass;

            try {
                $entity = $model::create($request->input());
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => $e->getMessage()], 404);
            }

            if (empty($entity->getKey())) {
                return response()->json(['error' => 'An internal server error has occurred.'], 500);
            } else {
                return response()->json($entity->toArray(), 201);
            }
        } catch (FatalErrorException $e) {
            return response()->json(['error' => 'An internal error has occurred'], 500);
        }
    }

    /**
     * Retrieves one record of a given entity by it's uuid
     *
     * @param Request $request - the request object
     * @param $uuid - the uuid of the entity to retrieve
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne(Request $request, $uuid)
    {
        try {
            $model = $this->modelClass;
            $entity = $model::uuid($uuid);
            return response()->json($entity->toArray());
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], 404);
        }
    }
    
    /**
     * Retrieves one record of a given entity by it's uuid,
     * then updates it with the data submitted in the request
     * 
     * @param Request $request - the request object
     * @param $uuid - the uuid of the entity to update
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOne(Request $request, $uuid)
    {
        try {
            $this->validateChildFormRequest($request, 'update');

            $model = $this->modelClass;
            $entity = $model::uuid($uuid);
            $entity->update($request->input());
            return response()->json($entity->toArray());
        } catch (FatalErrorException $e) {
            return response()->json(['error' => 'An internal error has occurred'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], 404);
        }
    }
    
    public function deleteOne(Request $request, $uuid)
    {
        try {
            $model = $this->modelClass;
            $entity = $model::uuid($uuid);
            if ($entity->delete()){
                return response()->make('', 204);
            } else {
                return response()->json(['error' => 'Something went wrong whilst deleting the record.'], 500);
            }
        } catch (ModelNotFoundException $e) {
            dd($e->getMessage());
            return response()->json(['error' => 'Record not found'], 404);
        }
    }

    /**
     * Retrieves all entities that belong to one record of a parent entity, based on the uuid of the parent entity.
     *
     * @param Request $request - the request object
     * @param $parent - the parent entity resource (from route)
     * @param $parent_uuid - the parent entity uuid (from route)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllForParent(Request $request, $parent, $parent_uuid)
    {
        try {
            $model = $this->modelClass;

            //get details for parent model, and how to retrieve child model from parent
            $parentModels = $model::$PARENT_MODELS;

            if (empty($parentModels[$parent]['class']) || empty($parentModels[$parent]['function'])) {
                throw new FatalErrorException('Parent model definition must include class and function name');
            }

            $parent = $parentModels[$parent];
            $parentClass = $parent['class'];
            $relationFunction = $parent['function'];

            //get the parent model
            $parentObject = $parentClass::uuid($parent_uuid);

            //ensure that the defined function exists to get from the parent model to the child model collection
            if (!method_exists($parentObject, $relationFunction)) {
                throw new FatalErrorException('Function ' . $relationFunction . ' did not exist on parent model class ' . $parentClass);
            }

            //retrieve child models, and return response.
            $children = $parentObject->{$relationFunction}()->get();
            return response()->json($children->toArray());
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], 404);
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
    private function validateChildFormRequest($request, $operation)
    {
        if (empty($operation) || empty($this->requestClasses[$operation])) {
            throw new FatalErrorException('Child FormRequest class must be defined with validation and authorize rules');
        }
        $childRequestClass = $this->requestClasses[$operation];
        $newRequest = $childRequestClass::createFromBase($request);
        $newRequest->setRouteResolver($request->getRouteResolver());
        $newRequest->setContainer(Container::getInstance());
        $newRequest->validate();
    }
}