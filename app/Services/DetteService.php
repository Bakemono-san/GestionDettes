<?php

namespace App\Services;

use App\Contracts\DetteRepositoryInt;
use App\Contracts\DetteServiceInt;
use App\Exceptions\ApiResponseException;
use App\Facades\DetteRepositoryFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetteService implements DetteServiceInt
{

    public function all(Request $request)
    {
        return DetteRepositoryFacade::all($request);
    }

    public function find($id)
    {
        return DetteRepositoryFacade::find($id);
    }

    public function create($data)
    {

        try {

            $dette = $data->only('montant','client_id');

            DB::beginTransaction();

            $dette = DetteRepositoryFacade::create($dette);
            DB::commit();
            return $dette;
        } catch (ApiResponseException $e) {
            DB::rollBack();
            return $e->getMessage();
        }



        // $articles = $data->input('articles');

        // Attach articles to dette with quantite and prixVente
        // foreach ($articles as $article) {
        //     $dette->articles()->attach($article['id'], [
        //         'quantite' => $article['quantite'],
        //         'prixVente' => $article['prixVente'],
        //     ]);

        //     // Update articles quantite after attach
        //     $articlefound = ArticleRepositoryFacade::find($article['id']);
        //     $article->update(['quantite' => $articlefound['quantite'] - $article['quantite']]);
        // }

        // if($data->has('paiement')){
        //     $paiement = $data->input('paiement');
        //     $paiement['dette_id'] = $dette->id;
        //     $this->paiementRepository->create($paiement);
        // }

    }

    public function update($id, $data)
    {
        return DetteRepositoryFacade::update($id, $data);
    }

    public function delete($id)
    {
        return DetteRepositoryFacade::delete($id);
    }

    public function findEtat($etat)
    {
        return DetteRepositoryFacade::findEtat($etat);
    }

    public function getArticles($id)
    {
        return DetteRepositoryFacade::getArticles($id);
    }

    public function getPaiements($id){
        return DetteRepositoryFacade::getPaiements($id);
    }

    public function payer($id, $montant){
        return DetteRepositoryFacade::payer($id, $montant);
    }
}