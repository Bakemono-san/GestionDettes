<?php

namespace App\Jobs;

use App\Contracts\SmsService;
use App\Facades\DetteRepositoryFacade;
use App\Facades\UserRepositoryFacade;
use App\Notifications\SendSms as NotificationsSendSms;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsPasser implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService)
    {
        $clients = DetteRepositoryFacade::getDetteNonSoldesPassed();
        
        foreach ($clients as $client) {
            // dd($client->user_id);
            $user = UserRepositoryFacade::find($client->user_id);
            
            // Envoyer un SMS à l'utilisateur avec les déttes non soldes
            $telephone = $client->telephone;
            $surname = $client->surname;
            $montantDette = $client->montant_restant;
            $message = 'Bonjour '.$surname.' Vous nous devez  a ce jour '.$montantDette.' fcfa. Et la date d\'echeance est passer';
            
            $user->notify(new NotificationsSendSms($telephone,$message));

        }

        return $clients;
    }
}
