<?php

namespace App\Facades;

use App\Contracts\QrCodeServiceInt;
use Illuminate\Support\Facades\Facade;

class QrCodeServiceFacade extends Facade {
    protected static function getFacadeAccessor() {
        return QrCodeServiceInt::class;
    }
}
