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


        if ($statut == 'Solde') {
            $dettes = $this->model->soldes()->with('client');
        } elseif ($statut == 'NonSolde') {
            $dettes = $this->model->nonSoldes()->with('client');
        }
        // $dettes->with('client');

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

    public function Restore($id)
    {
        $dette = $this->model->withTrashed()->find($id);
        $dette->restore();
    }

    public function getDetteNonSoldes()
    {
        $dettes = $this->model->nonSoldes()->with(['client'])->get();
        $dettesGroupes = $dettes->groupBy('client_id')->map(function ($group) {
            $client = $group->first()->client;

            $client->montant_restant = $group->sum('montant_restant');
            return $client;
        })->values();
        return $dettesGroupes;
    }

    public function getDetteNonSoldesByClient($id)
    {
        $dettes = $this->model->nonSoldes()
            ->with(['client'])  // Load the client relationship
            ->join('clients', 'dettes.client_id', '=', 'clients.id')
            ->where('clients.id', $id)
            ->select('dettes.*', 'clients.*')  // Select columns from both tables
            ->get();

        $dettesGroupes = $dettes->groupBy('client_id')->map(function ($group) {
            $client = $group->first()->client;  // Get the client from the first record

            // Calculate the remaining amount (montant_restant)
            $client->montant_restant = $group->sum(function ($dette) {
                return $dette->montant - $dette->paiements->sum('montant');  // Adjust according to your montant calculation
            });

            return $client->montant_restant;  // Return the client object with montant_restant
        })->values();

        return $dettesGroupes;
    }

    public function getDetteNonSoldesPassed(){
        $dateNow = date('Y-m-d');
        $dettes = $this->model->nonSoldes()
            ->with(['client'])  // Load the client relationship
            ->join('clients', 'dettes.client_id', '=', 'clients.id')
            ->whereDate('dettes.created_at', '<', $dateNow)
            ->select('dettes.*', 'clients.*')  // Select columns from both tables
            ->get();

        $dettesGroupes = $dettes->groupBy('client_id')->map(function ($group) {
            $client = $group->first()->client;  // Get the client from the first record

            // Calculate the remaining amount (montant_restant)
            $client->montant_restant = $group->sum(function ($dette) {
                return $dette->montant - $dette->paiements->sum('montant');
            });

            return $client;  // Return the client object with montant_restant
        })->values();

        return $dettesGroupes;
    }
}
