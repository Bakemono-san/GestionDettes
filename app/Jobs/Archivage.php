<?php

namespace App\Jobs;

use App\Contracts\ArchiveServiceInt;
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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
    public function handle(ArchiveServiceInt $archiveService): void
    {
        $clientDette = ClientRepositoryFacade::getClientWithDebtswithArticle();
        $archiveService->archive($clientDette);
        Log::debug('Clients archivees avec succès');
    }
}
