<?php

namespace App\Listeners;

use App\Events\asyncTransfertProcess;
use App\Jobs\ProcessClientData;
use App\Mail\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(asyncTransfertProcess $event): void
    {
        ProcessClientData::dispatch($event->client, $event->photoBase64, $event->user);
    }
}
