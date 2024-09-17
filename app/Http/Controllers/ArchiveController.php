<?php

namespace App\Http\Controllers;

use App\Facades\FirebaseServiceFacade;
use App\Facades\MongoServiceFacade;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getDebtByIdDette($id)
    {
       $dettes=  FirebaseServiceFacade::getArchivedDebtById($id);
    //    $dettes = MongoServiceFacade::getArchivedDebtById($id);
       return compact('dettes');
    }

    public function getByClient($id){
        $dettes=  FirebaseServiceFacade::GetArchivedDebtsByPhone($id);
        // $dettes = MongoServiceFacade::GetArchivedDebtsByPhone($id);
        return compact('dettes');
    }

    public function getAll(){
        $dettes = MongoServiceFacade::GetArchivedDebts();
        $dettes=  FirebaseServiceFacade::GetArchivedDebts();
        return compact('dettes');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function restoreById(string $id){
        $dette = FirebaseServiceFacade::RestoreById($id);
        // $dette = MongoServiceFacade::RestoreById($id);
        return compact('dette');
    }

    public function restoreByClient(string $id){
        FirebaseServiceFacade::restoreByClient($id);
        // MongoServiceFacade::restoreByClient($id);
        return response()->json(['message' => 'Les dettes du client ont été restaurées']);
    }

    public function restoreByDate(string $date){
        FirebaseServiceFacade::restoreByDate($date);
        // MongoServiceFacade::restoreByDate($date);
        return response()->json(['message' => 'Les dettes archivées pour la date '.$date.' ont été restaurées']);
    }
}
