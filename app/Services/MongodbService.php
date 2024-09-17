<?php

namespace App\Services;

use App\Facades\ClientRepositoryFacade;
use App\Models\MongoDette;
use Illuminate\Support\Facades\Log;
use App\Contracts\ArchiveServiceInt;
use App\Facades\DetteRepositoryFacade;

class MongodbService implements ArchiveServiceInt
{
    protected $archivedDebts = [];

    public function archive($request)
    {
        // Retrieve dettes from the facade
        $dettes = ClientRepositoryFacade::getClientWithDebtswithArticle();

        foreach ($dettes as $value) {
            $debts = $value['client']['debts'];
            $firstKey = $debts->keys()->first();
            if (in_array($firstKey, $this->archivedDebts)) {
                continue;
            }
            $this->archivedDebts[] = $firstKey;
        }

        Log::debug('Dettes non soldes', [
            'dettes' => $dettes
        ]);

        // Transform the data to match the required format
        $formattedData = $this->formatDettes($dettes);

        // Log formatted data for debugging
        Log::debug('Formatted Data', [
            'formatted' => $formattedData
        ]);

        foreach ($formattedData as $data) {
            $mongoDette = new MongoDette($data);
            $mongoDette->save();
        }
    }

    private function formatDettes($dettes)
    {
        // Initialize an empty array for formatted data
        $formattedData = [];

        // Loop through each item in the dettes collection
        foreach ($dettes as $clientId => $clientData) {
            $client = $clientData['client'];
            $debtsCollection = $client['debts'];


            // Prepare client data with nested debts and articles
            $formattedData[] = [
                'client' => [
                    'name' => $client['name'],
                    'phone' => $client['phone'],
                    'dettes' => $debtsCollection->mapWithKeys(function ($debt,$id) {
                        return [
                            $id => [
                                'amount' => $debt['amount'],
                                'status' => $debt['status'],
                                'articles' => $debt['articles']->map(function ($article) {
                                    // Handle articles; if there are no articles, it will return an empty array
                                    return [
                                        'article_name' => $article["name"] ?? 'Unknown',
                                        'price' => $article["price"] ?? 0
                                    ];
                                })->toArray(),
                            ]
                        ];
                    })->toArray()
                ],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return $formattedData;
    }

    public function GetArchivedDebts(){
        $dettes = MongoDette::all();
        return $dettes;
    }

    public function GetArchivedDebtsByPhone($id){
        $client = ClientRepositoryFacade::find($id);

        $debts = MongoDette::where('client.phone','=', "772157477")->get();
        return $debts;
    }

    public function getArchivedDebtById($idDette){
        $debt = MongoDette::where('client.dettes.'. $idDette, 'exists', true)->first();
        return $debt;
    }

    public function RestoreById($idDette){
        $debt = MongoDette::where('client.dettes.'. $idDette, 'exists', true)->first();
        DetteRepositoryFacade::restore($debt->id);
        $debt->delete();

        return $debt;
    }


    public function restoreByClient($clientId){
        $client = ClientRepositoryFacade::find($clientId);
        
        $dettes = MongoDette::where('client.phone','=', "772157477")->get();

        foreach($dettes as $dette){
            DetteRepositoryFacade::restore($dette->id);
            $dette->delete();
        }

        return $dettes;
    }

    public function restoreByDate($date){
        $dettes = MongoDette::all();

        foreach($dettes as $dette){
            DetteRepositoryFacade::restore($dette->id);
            $dette->delete();
        }

        return $dettes;
    }
    
}
