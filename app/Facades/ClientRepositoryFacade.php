<?php

namespace App\Facades;

use App\Contracts\ClientRepositoryInt as ContractsClientRepositoryInt;
use Illuminate\Support\Facades\Facade;

class ClientRepositoryFacade extends Facade {
    protected static function getFacadeAccessor() {
        return ContractsClientRepositoryInt::class;
    }
}
