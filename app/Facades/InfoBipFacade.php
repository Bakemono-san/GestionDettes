<?php

namespace App\Facades;

use App\Contracts\InfoBipServiceInt;
use App\Contracts\SmsService;
use Illuminate\Support\Facades\Facade;

class InfoBipFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SmsService::class;
    }
}
