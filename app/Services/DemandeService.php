<?php

namespace App\Services;

use App\Contracts\DemandeServiceInt;
use App\Facades\ClientRepositoryFacade;
use App\Facades\DemandeServiceFacade;
use App\Facades\DetteRepositoryFacade;
use App\Facades\UserRepositoryFacade;
use App\Models\Demandes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemandeService implements DemandeServiceInt
{

    public function createDemande($data)
    {
        $datas = $data->only('montant', 'client_id');
        $datas['etat'] = 'En attente';

        $client = ClientRepositoryFacade::find($data->input('client_id'));
        $user = UserRepositoryFacade::find($client->user_id);
        $dettes = DetteRepositoryFacade::getDetteNonSoldesByClient($data->input('client_id'));


        if (count($dettes) > 0) {

            if (count($dettes) > 0 &&  $user->role->id == 2 && $dettes[0] + $datas['montant'] > $client->montant_max) {
                return "Vous ne pouvez pas avoir plus de dettes veuillez payer vos dette antecedent";
            }

            if (count($dettes) > 0 && $user->role->id == 3 & $dettes[0] > 0) {
                return "Vous ne pouvez pas passer une demande car vous avez une dettes";
            }
        }

        DB::beginTransaction();
        $demande = Demandes::create($datas);

        $articles = $data->input('articles');
        foreach ($articles as $article) {
            $demande->articles()->attach($article['id'], ['quantite' => $article['quantite']]);
        }

        DB::commit();

        return $demande;
    }

    public function relance($demande)
    {
        if (($demande->etat == 'Annule' || $demande->etat == 'annule')) {
            $now = new \DateTime();
            $updatedAt = new \DateTime($demande->updated_at);
            $interval = $now->diff($updatedAt);
            if ($interval->days > 2) {
                return "fallait relancer avant 2 jours ";
            }
            $client = ClientRepositoryFacade::find($demande->client_id);
            $user = UserRepositoryFacade::find($client->user_id);
            DemandeServiceFacade::sendNotificationAnnulation($demande->client_id, 'une relance de demande a ete enregistre.', 'relance');

            // $user->notify(new \App\Notifications\SendSms('+221785953562', 'Une demande de création de compte a été reçue','demande'));
            $demande->etat = 'En attente';
            $demande->save();
        }

        return $demande;
    }

    public function sendNotificationAnnulation($clientId, $message, $type)
    {
        $client = ClientRepositoryFacade::find($clientId);
        $user = UserRepositoryFacade::find($client->user_id);

        $user->notify(new \App\Notifications\SendSms($client->telephone, $message, $type));

        return $client;
    }

    public function getDemandeService($idDemande)
    {
        $demandes = Demandes::find($idDemande);

        $articles = $demandes->articles;
        $articlesStats = [];
        $articlesDisponibles = [];
        $indisponibles = null;
        foreach ($articles as $article) {
            $articlesStats[] = [
                "article_id" => $article->id,
                "article_name" => $article->libelle,
                "article_quantite_commande" => $article->pivot->quantite,
                "article_quantite" => $article->quantite - $article->seuil,
                "disponible" => $article->quantite - $article->seuil - $article->pivot->quantite > 0 ? true : false,
            ];
            if ($article->quantite - $article->seuil - $article->pivot->quantite > 0) {
                $articlesDisponibles[] = [
                    "article_id" => $article->id,
                    "article_name" => $article->libelle,
                    "article_quantite_commande" => $article->pivot->quantite
                ];
            } else {
                $indisponibles = true;
            }
        }

        if ($indisponibles == true) {
            $client = ClientRepositoryFacade::find($demandes->client_id);
            $user = UserRepositoryFacade::find($client->user_id);


            $articleNames = array_column($articlesDisponibles, 'article_name');

            $articlesDisponiblesString = implode(", ", $articleNames);
            $user->notify(new \App\Notifications\SendSms($client->telephone, 'Vous avez des articles indisponibles.Articles disponible: ' . $articlesDisponiblesString, 'demande'));
        }

        return $articlesStats;
    }

    public function traiterDemande($request, $id)
    {
        $demandes = Demandes::find($id);

        $reponse = $request->input('reponse');

        if ($reponse == "Valider") {
            $montant = $demandes->montant;
            $clientId = $demandes->client_id;

            $data = ["montant" => $montant, "client_id" => $clientId];


            $articles = $demandes->articles;
            DB::beginTransaction();
            $dette = DetteRepositoryFacade::create($data);

            $articleDettes = [];
            foreach ($articles as $article) {
                $articleDettes[] = [
                    "article_id" => $article->id,
                    "dette_id" => $dette->id,
                    "quantite" => $article->pivot->quantite,
                    "prixVente" => 0,
                ];
            }
            $dette->articles()->attach($articleDettes);

            DB::commit();

            $demandes->delete();
            DemandeServiceFacade::sendNotificationAnnulation($demandes->client_id, 'veuillez venir recuperer vos articles la dette a ete accepter.', 'reponse');

            return $dette;
        } else {
            // send notification
            DemandeServiceFacade::sendNotificationAnnulation($demandes->client_id, 'annulation de votre demande de dette.', 'demande', 'reponse');
            $demandes->etat = 'Annule';
            $demandes->save();

            return "Demande annulée";
        }
    }
}
