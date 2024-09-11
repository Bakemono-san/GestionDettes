<?php

namespace App\Facades;

use App\Contracts\ClientServiceInt;
use Illuminate\Support\Facades\Facade;

class ClientServiceFacade extends Facade {
    protected static function getFacadeAccessor() {
        return ClientServiceInt::class;
    }
}
