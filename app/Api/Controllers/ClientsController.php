<?php

namespace Api\Controllers;

use Api\Requests\ClientRequest;
use Api\Controllers\RestResourceController;
use App\Client;

class ClientsController extends RestResourceController
{
    public function __construct()
    {
        $this->modelClass = Client::class;
        $this->requestClass = ClientRequest::class;

        parent::__construct();
    }
}