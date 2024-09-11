<?php

namespace App\Services;

use App\Facades\ClientRepositoryFacade;
use App\Models\MongoDette;
use Illuminate\Support\Facades\Log;
use App\Contracts\ArchiveServiceInt;

class MongodbService implements ArchiveServiceInt{


    public function archive($request){
        // Retrieve dettes from the facade
        $dettes = ClientRepositoryFacade::getClientWithDebtswithArticle();

        
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
                    'dettes' => $debtsCollection->map(function ($debt) {
                        return [
                            'amount' => $debt['amount'],
                            'status' => $debt['status'],
                            'articles' => $debt['articles']->map(function ($article) {
                                // Handle articles; if there are no articles, it will return an empty array
                                return [
                                    'article_name' => $article["name"] ?? 'Unknown',
                                    'price' => $article["price"] ?? 0
                                ];
                            })->toArray(),
                        ];
                    })->toArray()
                ],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return $formattedData;
    }
}
