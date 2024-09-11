<?php

namespace App\Facades;

use App\Contracts\UserServiceInt;
use Illuminate\Support\Facades\Facade;

class UserServiceFacade extends Facade{
    protected static function getFacadeAccessor() {
        return UserServiceInt::class;
    }
}