<?php

namespace App\Services;

use App\Models\MongoDette;
use Illuminate\Support\Facades\Log;

class ArchiveDataRecuperation
{

    protected $dettes = [];

    public function store($dettes,$service){
        $this->dettes[$service] = $dettes;
    }
    public function restoreByDate($date)
    {
        $dettes = MongoDette::where('created_at', '=', $date)->get();

        foreach ($dettes as $dette) {
            DetteRepositoryFacade::restore($dette->id);
            $dette->delete();
        }

        return $dettes;
    }
}