<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use App\Contracts\ArchiveServiceInt;

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
        $newData = $this->database->getReference(date('Y-m-d H:i:s'))->push($request);
        return response()->json($newData->getValue());
    }
}
