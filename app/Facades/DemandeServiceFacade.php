<?php

namespace App\Facades;

use App\Contracts\DemandeServiceInt;
use Illuminate\Support\Facades\Facade;

class DemandeServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DemandeServiceInt::class;
    }
}