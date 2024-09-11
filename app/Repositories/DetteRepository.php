<?php

namespace App\Repositories;

use App\Contracts\DetteRepositoryInt;
use App\Models\Client;
use App\Models\Dette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DetteRepository implements DetteRepositoryInt
{
    protected $model;

    public function __construct(Dette $model)
    {

        $this->model = $model;
    }

    public function all(Request $request)
    {
        $statut = $request->query('statut');

        $dettes = QueryBuilder::for(Dette::class);


        if ($statut === 'Solde') {
            $dettes = $dettes->soldes();
        } elseif ($statut === 'NonSolde') {
            $dettes = $dettes->nonSoldes();
        }
        $dettes->with('client');

        return $dettes->get();
    }

    public function find($id)
    {
        return $this->model->with('client')->find($id);
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

    public function findEtat($etat)
    {
        return $this->model->where('etat', $etat)->get();
    }

    public function getArticles($id)
    {
        return $this->model->with('articles')->find($id);
    }

    public function getPaiements($id)
    {
        return $this->model->with('paiements')->find($id);
    }

    public function payer($id, $montant)
    {
        $dette = $this->model->find($id);
        $dette->paiements()->create(['montant' => $montant, 'dette_id' => $id]);
        return $dette;
    }

    public function getDetteNonSoldes()
    {
        $dettes= $this->model->nonSoldes()->with('client')->get();
        $dettesGroupes = $dettes->groupBy('client_id')->map(function ($group) {
            $client = $group->first()->client;

            $client->montant_restant = $group->sum('montant_restant');
            return $client;
        })->values();
        return $dettesGroupes;
    }
}
