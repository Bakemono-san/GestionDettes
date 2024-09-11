<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface DetteRepositoryInt
{
    public function all(Request $request);
    public function find($id);
    public function create($data);
    public function update($id,  $data);
    public function delete($id);
    public function findEtat($etat);
    public function getArticles($id);
    public function getPaiements($id);
    public function payer($id, $montant);
    public function getDetteNonSoldes();
}
