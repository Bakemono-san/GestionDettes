<?php

namespace App\Facades;

use App\Contracts\DetteRepositoryInt;
use Illuminate\Support\Facades\Facade;

class DetteRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DetteRepositoryInt::class;
    }
}
