<?php

namespace App\Services;

use App\Contracts\PaiementRepositoryInt;
use App\Contracts\PaiementServiceInt;
use App\Facades\PaiementRepositoryFacade;

class PaiementService implements PaiementServiceInt
{
    public function all()
    {
        return PaiementRepositoryFacade::all();
    }

    public function find($id)
    {
        return PaiementRepositoryFacade::find($id);
    }

    public function create($data)
    {
        return PaiementRepositoryFacade::create($data);
    }

    public function update($id,  $data)
    {
        return PaiementRepositoryFacade::update($id, $data);
    }

    public function delete($id)
    {
        return PaiementRepositoryFacade::delete($id);
    }
}