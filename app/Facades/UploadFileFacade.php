<?php

namespace App\Facades;

use App\Contracts\UploadImageServiceInt;
use Illuminate\Support\Facades\Facade;

class UploadFileFacade extends Facade {
    protected static function getFacadeAccessor() {
        return UploadImageServiceInt::class;
    }
}
