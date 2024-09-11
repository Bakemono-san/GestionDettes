<?php

namespace App\Facades;

use App\Contracts\PaiementRepositoryInt;
use Illuminate\Support\Facades\Facade;

class PaiementRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PaiementRepositoryInt::class;
    }
}
