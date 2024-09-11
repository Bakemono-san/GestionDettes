<?php

namespace App\Http\Controllers;

use App\Facades\PaiementServiceFacade;
use App\Http\Requests\StorePaiementRequest;
use App\Models\Paiement;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Paiement API",
 *     version="1.0.0",
 *     description="API for managing payments."
 * )
 *
 * @OA\Tag(
 *     name="Paiements",
 *     description="Operations related to payments"
 * )
 *
 * @OA\PathItem(
 *     path="/api/paiements"
 * )
 */
class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    /**
     * @OA\Post(
     *     path="/api/paiements",
     *     tags={"Paiements"},
     *     summary="Create a new payment",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePaiementRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Paiement")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StorePaiementRequest $request)
    {
        return PaiementServiceFacade::create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paiement $paiement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paiement $paiement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiement $paiement)
    {
        //
    }
}
