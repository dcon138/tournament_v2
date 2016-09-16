<?php

namespace Api\Controllers;

use Api\Requests\CreateUserRequest;
use Api\Requests\UpdateUserRequest;
use Api\Controllers\RestResourceController;
use App\User;

class UsersController extends RestResourceController
{
    public function __construct()
    {
        $this->modelClass = User::class;
        $this->requestClasses = [
            'create' => CreateUserRequest::class,
            'update' => UpdateUserRequest::class,
        ];

        parent::__construct();
    }
}