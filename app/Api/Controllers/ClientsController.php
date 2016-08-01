<?php

namespace Api\Controllers;

use Illuminate\Http\Request;
use Api\Controllers\RestResourceController;
use App\Client;

class ClientsController extends RestResourceController
{
    public function __construct()
    {
        $this->modelClass = Client::class;
        $this->requestClass = Request::class; //TODO implement ClientRequest
    }
}