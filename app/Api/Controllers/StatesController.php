<?php

namespace Api\Controllers;

use Api\Requests\StateRequest;
use App\State;
use Api\Controllers\RestResourceController;

class StatesController extends RestResourceController
{
    public function __construct()
    {
        $this->modelClass = State::class;
        $this->requestClass = StateRequest::class;

        parent::__construct();
    }
}