<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MongoServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mongoService';
    }
}