<?php

namespace App\Contracts;

use App\Http\Requests\StoreClientRequest;

interface ClientServiceInt{
    public function all();
    public function find($id);
    public function create(StoreClientRequest $client);
    public function update($id, array $data);
    public function delete($id);
    public function dettes($id);
    public function getClientWithDebtswithArticle();
}