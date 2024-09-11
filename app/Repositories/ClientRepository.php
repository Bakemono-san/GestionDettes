<?php

namespace App\Repositories;

use App\Contracts\ClientRepositoryInt;
use App\Models\Client;
use App\Models\Dette;
use App\Models\Scopes\TelephoneScope;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class ClientRepository implements ClientRepositoryInt
{
    protected $model;
    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    public function all($filters = [])
    {
        $clientsQuery = $this->model->query();

        if (isset($filters["active"])) {
            $clientsQuery->etat($filters["active"]);
        }

        if (isset($filters["compte"])) {
            $clientsQuery->compte($filters["compte"]);
        }

        if (isset($filters["include"])) {
            $clientsQuery->withUser();
        }

        if (isset($filters["telephone"])) {
            $clientsQuery->withGlobalScope('telephone', new TelephoneScope($filters["telephone"]))->get();
        }
        $clientsQuery->with('user');

        return $clientsQuery->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($clientData)
    {
        // Create the client
        $client = $this->model->create($clientData);

        return $client;
    }

    public function update($id, array $data)
    {
        // Update the client
        $client = $this->model->find($id);
        $client->update($data);

        $client->save();

        return $client;
    }

    public function delete($id)
    {
        $client = $this->model->find($id);
        $client->delete();

        return $client;
    }

    public function get($id)
    {
        $compte = QueryBuilder::for(Client::class)
            ->where('id', (int)$id)
            ->with('user')
            ->get();
        return $compte;
    }

    public function dettes($id)
    {

        $client = QueryBuilder::for(Client::class)
            ->where('id', (int)$id)
            ->with('dettes')
            ->first();

        return $client;
    }

    public function getClientWithDebtswithArticle()
{
    $clients = QueryBuilder::for(Client::class)
        ->with(['dettes' => function ($query) {
            // Only include settled debts
            $query->soldes()->with('articles');
        }])
        ->get()
        ->filter(function ($client) {
            // Only include clients that have settled debts
            return $client->dettes->isNotEmpty();
        })
        ->map(function ($client) {
            // Wrap the client data within a 'client' key
            return [
                'client' => [
                    'name' => $client->surname,
                    'phone' => $client->telephone,
                    'debts' => $client->dettes->map(function ($dette) {
                        return [
                            'amount' => $dette->montant,
                            'status' => 'settled',
                            'articles' => $dette->articles->mapWithKeys(function ($article) {
                                return [
                                    $article->id => [
                                        'name' => $article->libelle,
                                        'price' => $article->prix,
                                    ]
                                ];
                            }),
                        ];
                    }),
                ]
            ];
        });

    // Return or output the result
    echo $clients;
    dd($clients);

    return $clients;
}

}
