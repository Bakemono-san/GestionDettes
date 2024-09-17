<?php

namespace App\Observers;

use App\Jobs\attachAndUpdate;
use App\Jobs\paiement;
use App\Models\Dette;
use Illuminate\Support\Facades\DB;

class DetteObserver
{
    /**
     * Handle the Dette "created" event.
     */
    public function created(Dette $dette): void
    {
        DB::transaction(function () use ($dette) {

            $dette->save();
            $request = request();

            $articles = $request->input('articles');
            if ($request->has('articles')) {
                attachAndUpdate::dispatch($articles, $dette);
            }

            if ($request->has('paiement')) {
                $paiement = $request->input('paiement');
                $paiement['dette_id'] = $dette->id;
                paiement::dispatch($paiement);
            }
        });
    }

    /**
     * Handle the Dette "updated" event.
     */
    public function updated(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "deleted" event.
     */
    public function deleted(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "restored" event.
     */
    public function restored(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "force deleted" event.
     */
    public function forceDeleted(Dette $dette): void
    {
        //
    }
}
