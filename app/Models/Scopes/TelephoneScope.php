<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TelephoneScope implements Scope
{
    protected $telephone;

    public function __construct(string $telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('telephone', 'like', '%' . $this->telephone . '%');
    }
}
