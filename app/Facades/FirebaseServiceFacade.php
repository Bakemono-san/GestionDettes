<?php

namespace App\Facades;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Facade;

class FirebaseServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase';
    }
}