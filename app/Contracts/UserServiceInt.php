<?php

namespace App\Contracts;

interface UserServiceInt{
    public function all();
    public function find($id);
    public function create($user);
    public function update($id, array $data);
    public function delete($id);

}