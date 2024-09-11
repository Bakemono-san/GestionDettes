<?php

namespace App\Jobs;

use App\Facades\ClientRepositoryFacade;
use App\Facades\FirebaseServiceFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Archivage implements ShouldQueue
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
        $clientDette = ClientRepositoryFacade::getClientWithDebtswithArticle();
        FirebaseServiceFacade::store($clientDette);
        Log::debug('Clients archivees avec succès');
    }
}
