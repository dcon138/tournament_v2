<?php

namespace Api\Controllers;

use Api\Requests\ClientGroupRequest as Request;
use App\ClientGroup;
use Api\Controllers\RestResourceController;

class ClientGroupsController extends RestResourceController
{
    public function __construct()
    {
        $this->modelClass = ClientGroup::class;
        $this->requestClass = Request::class;
    }
}