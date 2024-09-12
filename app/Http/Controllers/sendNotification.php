<?php

namespace App\Http\Controllers;

use App\Facades\ClientRepositoryFacade;
use App\Facades\DetteRepositoryFacade;
use App\Facades\UserRepositoryFacade;
use Illuminate\Http\Request;

class sendNotification extends Controller
{
    public function sendGroupe(Request $request){
        $client = $request->input('clients');

        foreach($client as $value){
            $client = ClientRepositoryFacade::find($value['id']);
            $user = UserRepositoryFacade::find($client->user_id);

            $montant_Du = DetteRepositoryFacade::getDetteNonSoldesByClient($value['id']);
            $user->notify(new \App\Notifications\SendSms($client->telephone, 'Vous nous devez a ce jour '.$montant_Du[0].' FCFA. merci de bien vouloir regler votre dette avant la fin du mois'));
        }

        return $client;
    }

    public function sendGroupeMessage(Request $request){
        $client = $request->input('clients');

        foreach($client as $value){
            $client = ClientRepositoryFacade::find($value['id']);
            $user = UserRepositoryFacade::find($client->user_id);

            $montant_Du = DetteRepositoryFacade::getDetteNonSoldesByClient($value['id']);
            $user->notify(new \App\Notifications\SendSms($client->telephone,$request->input('message')));
        }

        return $client;
    }

    


}
