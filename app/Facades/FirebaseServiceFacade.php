<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FirebaseServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase';
    }
}