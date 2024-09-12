<?php

namespace App\Observers;

use App\Jobs\demandeCreation;

class CreateDemande
{
    public function created(){
        demandeCreation::dispatch();
    }
}
