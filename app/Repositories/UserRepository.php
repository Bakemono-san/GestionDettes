<?php

namespace App\Repositories;

use App\Contracts\ClientRepositoryInt;
use App\Contracts\UserRepositoryInt;
use App\Models\Scopes\TelephoneScope;
use App\Models\User;

class UserRepository implements UserRepositoryInt{
    protected $model;
    public function __construct(User $user){
        $this->model = $user;
    }

    public function all($filters = []){
        $clientsQuery = $this->model->query();

        if(isset($filters["active"])){
            $clientsQuery->etat($filters["active"]);
        }

        if(isset($filters["compte"])){
            $clientsQuery->compte($filters["compte"]);
        }

        if(isset($filters["include"])){
            $clientsQuery->withUser();
        }

        if(isset($filters["telephone"])){
            $clientsQuery->withGlobalScope('telephone',new TelephoneScope($filters["telephone"]))->get();
        }
        $clientsQuery->with('user');

        return $clientsQuery->get();
    }

    public function find($id){
        return $this->model->find($id);
    }

    public function create($data){
        // Create the client
        $user = $this->model->create($data);

        return $user;
    }

    public function update($id, array $data){
        // Update the client
        $user = $this->model->find($id);
        $user->update($data);

        $user->save();

        return $user;
    }

    public function delete($id){
        $user = $this->model->find($id);
        $user->delete();

        return $user;
    }

    public function findByLogin($login){
        return $this->model->where('login', $login)->first();
    }
}