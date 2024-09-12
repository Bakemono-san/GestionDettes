<?php

namespace App\Repositories;

use App\Contracts\ClientRepositoryInt;
use App\Facades\DetteRepositoryFacade;
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

    public function getByUserId($userId){
        return $this->model->where('user_id', $userId)->first();
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
                $query->soldes()->with('articles');
            }])
            ->get()
            ->filter(function ($client) {
                return $client->dettes->isNotEmpty();
            })
            ->map(function ($client) {
                return [
                    'client' => [
                        'name' => $client->surname,
                        'phone' => $client->telephone,
                        'debts' => $client->dettes->mapWithKeys(function ($dette) {
                            return [
                                $dette->id => [
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
                                ]
                                // DetteRepositoryFacade::delete($dette->id)

                            ];
                        }),
                    ]
                ];
            });

        $datas = [];
        foreach ($clients as $key => $client) {
            $datas[] = $client;
        }

        // Return or output the result

        return $datas;
    }
}
