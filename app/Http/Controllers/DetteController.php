<?php

namespace App\Http\Controllers;

use App\Facades\DetteServiceFacade;
use App\Http\Requests\StoreDetteRequest;
use App\Http\Requests\StorePaiementRequest;
use App\Models\Dette;
use Illuminate\Http\Request;

class DetteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $dettes = DetteServiceFacade::all($request);
        return compact('dettes');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDetteRequest $request)
    {
        $dette = DetteServiceFacade::create($request);
        return compact('dette');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dette = DetteServiceFacade::find($id);
        return compact('dette');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dette $dette)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dette $dette)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dette $dette)
    {
        //
    }
    
    public function getArticles($id){
        $dette = DetteServiceFacade::getArticles($id);
        return compact('dette');
    }

    public function getPaiements($id){
        $paiements = DetteServiceFacade::getPaiements($id);
        return compact('paiements');
    }

    public function payer(StorePaiementRequest $request,$id){
        $montant = $request->input('montant');
        $dette = DetteServiceFacade::payer($id, $montant);
        return compact('dette');
    }
}
