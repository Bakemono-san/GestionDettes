<?php

namespace App\Facades;

use App\Contracts\ArticleRepositoryImpl;
use Illuminate\Support\Facades\Facade;

class ArticleRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ArticleRepositoryImpl::class;
    }
}
