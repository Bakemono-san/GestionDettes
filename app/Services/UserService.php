<?php

namespace App\Services;

use App\Contracts\UserServiceInt;
use App\Facades\UserRepositoryFacade;

class UserService implements UserServiceInt{

    public function all(){
        return UserRepositoryFacade::all();
    }

    public function find($id){
        return UserRepositoryFacade::find($id);
    }
    public function create($user){
        return UserRepositoryFacade::create($user);
    }
    public function update($id, array $data){
        return UserRepositoryFacade::update($id,$data);
    }
    public function delete($id){
        return UserRepositoryFacade::delete($id);
    }
}
