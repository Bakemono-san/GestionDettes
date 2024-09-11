<?php

namespace App\Contracts;

interface ClientRepositoryInt{
    public function all($filters = []);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function dettes($id);
    public function getClientWithDebtswithArticle();
}
