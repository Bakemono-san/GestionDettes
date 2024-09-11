<?php

namespace App\Jobs;

use App\Facades\PdfServiceFacade;
use App\Facades\QrCodeServiceFacade;
use App\Mail\SendMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessClientData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $client;
    protected $photoBase64;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($client, $photoBase64, $user)
    {
        $this->client = $client;
        $this->photoBase64 = $photoBase64;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Generate the PDF
        $pdf = PdfServiceFacade::generatePdf2($this->client->qrcode, $this->photoBase64, $this->user);

        // Send email with the PDF attached
        Mail::to($this->client->adresse)->send(new SendMail($this->user, $pdf));

    }
}
