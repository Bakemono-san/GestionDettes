<?php

namespace App\Console\Commands;

use App\Jobs\RelanceJob;
use Illuminate\Console\Command;

class Relance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:relance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        RelanceJob::dispatch();
        $this->info("Dispatched job to process file");
    }
}
