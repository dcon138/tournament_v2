<?php

namespace Api\Controllers;

use Api\Requests\UserRequest;
use Api\Controllers\RestResourceController;
use App\User;

class UsersController extends RestResourceController
{
    public function __construct()
    {
        $this->modelClass = User::class;
        $this->requestClass = UserRequest::class;

        parent::__construct();
    }
}