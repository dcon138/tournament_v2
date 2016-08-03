<?php

namespace Api\Controllers;

use Api\Requests\ClientGroupRequest;
use App\ClientGroup;
use Api\Controllers\RestResourceController;

class ClientGroupsController extends RestResourceController
{
    public function __construct()
    {
        $this->modelClass = ClientGroup::class;
        $this->requestClass = ClientGroupRequest::class;

        parent::__construct();
    }
}