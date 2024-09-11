<?php

namespace App\Repositories;

use App\Contracts\PaiementRepositoryInt;
use App\Models\Paiement;

class PaiementRepository implements PaiementRepositoryInt
{
    protected $model;
    public function __construct(Paiement $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id,  $data)
    {
        $dette = $this->model->find($id);
        $dette->update($data);
        return $dette;
    }

    public function delete($id)
    {
        $dette = $this->model->find($id);
        $dette->delete();
    }
}