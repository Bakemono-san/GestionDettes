<?php

namespace App\Facades;

use App\Contracts\PaiementServiceInt;
use Illuminate\Support\Facades\Facade;

class PaiementServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PaiementServiceInt::class;
    }
}
