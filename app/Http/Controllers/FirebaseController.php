<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase->getDatabase();
    }

    public function index()
    {
        $reference = $this->firebase->getReference('test'); // Specify your data path
        $snapshot = $reference->getSnapshot();
        $value = $snapshot->getValue();

        return response()->json($value); // Return the data as JSON
    }

    public function store(Request $request)
    {
        $newData = $this->firebase->getReference('test')->push($request->all());
        return response()->json($newData->getValue());
    }
}
