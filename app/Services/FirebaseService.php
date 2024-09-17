<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use App\Contracts\ArchiveServiceInt;
use App\Facades\ClientRepositoryFacade;
use App\Facades\DetteRepositoryFacade;

class FirebaseService implements ArchiveServiceInt
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials_file'))
            ->withDatabaseUri('https://gestiondettes-b74ca-default-rtdb.firebaseio.com/');  // Replace with your Firebase Realtime Database URL

        $this->database = $factory->createDatabase();
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function archive($request)
    {
        $newData = $this->database->getReference('archives/'.date('Y-m-d').'/')->push($request);
      
        return response()->json($newData->getValue());
    }

    public function GetArchivedDebts(){
        // get all archved debts with their clients without carying their date
        $debts = $this->database->getReference('archives')->getValue();
        
        return $debts;
    }

    public function GetArchivedDebtsByDate($date) {
        $debts = $this->database->getReference('archives/'.$date)->getValue();

        $debts['date'] = $date;
        
        return $debts;
    }

    public function GetArchivedDebtsByPhone($id){
        $client = ClientRepositoryFacade::find($id);
        $telephone = $client->telephone;

        $debts = $this->database->getReference('archives')->getValue();

        $filteredResults = [];
        foreach ($debts as $dateKey => $dateData) {
            // Loop through each record under the date
            foreach ($dateData as $recordKey => $recordData) {
                $recordData[0]['client']['debts'] = array_filter($recordData[0]['client']['debts']);
                if (isset($recordData[0]['client']['phone']) && $recordData[0]['client']['phone'] === $telephone) {
                    $recordData["date"] = $dateKey;
                    $recordData['recordKey'] = $recordKey;
                    $filteredResults[] = $recordData;
                }
            }
        }
        return $filteredResults;
    }

    public function getArchivedDebtById($idDette){
        $debts = $this->database->getReference('archives')->getValue();

        $filteredResults = null;
        foreach ($debts as $dateKey => $dateData) {
            foreach ($dateData as $recordKey => $recordData) {
                $recordData[0]['client']['debts'] = array_filter($recordData[0]['client']['debts']);
                if (key($recordData[0]['client']['debts']) == $idDette) {
                    $recordData['recordKey'] = $recordKey;
                    $recordData['date'] = $dateKey;
                    $filteredResults = $recordData;
                }
            }
        }
        return $filteredResults;
    }

    public function RestoreById($idDette){
        
        $debt = $this->getArchivedDebtById($idDette);

        if(!$debt){
            return "dette non trouve";
        }

        $this->database->getReference('archives/'.$debt['date'].'/'.$debt['recordKey'].'/0/client/debts/'.(int)$idDette)->remove();


        $dette = DetteRepositoryFacade::restore($idDette);
        return $dette;
    }

    public function restoreByClient($clientId){
        $archivedDebts = $this->GetArchivedDebtsByPhone($clientId);

        $date = null;
        $dette_ids = [];
        foreach($archivedDebts as $dette){
            $date = $dette['date'];
            
            $dette_ids[] = key($dette[0]['client']['debts']);
        }
        foreach($archivedDebts as $dette){
            $this->database->getReference('archives/'.$date.'/'.$dette['recordKey'])->remove();
        }

        foreach($dette_ids as $id){
            DetteRepositoryFacade::restore($id);
        }

        return $archivedDebts;
    }

    public function restoreByDate($date)
{
    $dettes = $this->GetArchivedDebtsByDate($date);
    
    $detteRestored = [];
    
    foreach ($dettes as $key => $value) {
        if (isset($value[0]['client']['debts']) && is_array($value[0]['client']['debts'])) {
            $value[0]['client']['debts'] = array_filter($value[0]['client']['debts']);
            
            foreach ($value[0]['client']['debts'] as $debtKey => $debt) {
                
                // Restore the debt
                $detteRestored[] = DetteRepositoryFacade::restore($debtKey);
            }
        }
        
        // After restoring, remove the archived data for the given date
        $this->database->getReference('archives/'.$date)->remove();
    }
    
    return $detteRestored;
}

}
