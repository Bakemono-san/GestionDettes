<?php

namespace App\Facades;

use App\Contracts\DetteServiceInt;
use Illuminate\Support\Facades\Facade;

class DetteServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DetteServiceInt::class;
    }
}