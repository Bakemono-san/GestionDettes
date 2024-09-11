<?php

namespace App\Jobs;

use App\Facades\DetteRepositoryFacade;
use App\Facades\InfoBipFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSms implements ShouldQueue
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
    public function handle(): void
    {
        $clients = DetteRepositoryFacade::getDetteNonSoldes();
        
        foreach ($clients as $client) {
            // Envoyer un SMS à l'utilisateur avec les déttes non soldes
            $telephone = $client->telephone;
            $surname = $client->surname;
            $montantDette = $client->montant_restant;
            $message = 'Bonjour '.$surname.' Vous nous devez  a ce jour '.$montantDette.' fcfa';

            InfoBipFacade::sendSms($telephone,$message);
        }
    }
}
