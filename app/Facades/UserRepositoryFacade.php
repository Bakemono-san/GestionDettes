<?php

namespace App\Facades;

use App\Contracts\UserRepositoryInt;
use Illuminate\Support\Facades\Facade;

class UserRepositoryFacade extends Facade {
    protected static function getFacadeAccessor() {
        return UserRepositoryInt::class;
    }
}
