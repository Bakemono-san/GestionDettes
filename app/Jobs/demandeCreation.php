<?php

namespace App\Jobs;

use App\Facades\UserRepositoryFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class demandeCreation implements ShouldQueue
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
        $boutiquiers = UserRepositoryFacade::getBoutiquiers();

        foreach ($boutiquiers as $boutiquier) {
            $boutiquier->notify(new \App\Notifications\SendSms('785953562', 'Une demande de création de compte a été reçue','demande'));
        }
    }
}
