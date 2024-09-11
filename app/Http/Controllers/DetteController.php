<?php

namespace App\Http\Controllers;

use App\Facades\DetteServiceFacade;
use App\Http\Requests\StoreDetteRequest;
use App\Http\Requests\StorePaiementRequest;
use App\Models\Dette;
use Illuminate\Http\Request;


/**
 * @OA\Tag(name="Dette", description="Operations related to debts")
 */
class DetteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/v1/dettes",
     *     tags={"Dette"},
     *     summary="Get a list of all dettes",
     *     description="Returns all debts",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of dettes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="dettes", type="array", @OA\Items(ref="#/components/schemas/Dette"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
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

    /**
     * @OA\Post(
     *     path="/v1/dettes",
     *     tags={"Dette"},
     *     summary="Create a new dette",
     *     description="Creates a new debt record",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreDetteRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dette created",
     *         @OA\JsonContent(ref="#/components/schemas/Dette")
     *     ),
     *     @OA\Response(response=400, description="Invalid data")
     * )
     */
    public function store(StoreDetteRequest $request)
    {
        $dette = DetteServiceFacade::create($request);
        return compact('dette');
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/v1/dettes/{id}",
     *     tags={"Dette"},
     *     summary="Get a specific dette",
     *     description="Returns a specific debt record by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the dette",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dette found",
     *         @OA\JsonContent(ref="#/components/schemas/Dette")
     *     ),
     *     @OA\Response(response=404, description="Dette not found")
     * )
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
    public function destroy($id)
    {
        $dette = DetteServiceFacade::delete($id);
        return compact('dette');
    }

    /**
     * @OA\Get(
     *     path="/v1/dettes/{id}/articles",
     *     tags={"Dette"},
     *     summary="Get articles related to a dette",
     *     description="Returns the articles linked to a specific dette",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the dette",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles related to the dette",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article"))
     *     ),
     *     @OA\Response(response=404, description="Dette not found")
     * )
     */
    public function getArticles($id)
    {
        $dette = DetteServiceFacade::getArticles($id);
        return compact('dette');
    }

    /**
     * @OA\Get(
     *     path="/v1/dettes/{id}/paiements",
     *     tags={"Dette"},
     *     summary="Get paiements related to a dette",
     *     description="Returns the list of paiements for a specific dette",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the dette",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of paiements related to the dette",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Paiement"))
     *     ),
     *     @OA\Response(response=404, description="Dette not found")
     * )
     */
    public function getPaiements($id)
    {
        $paiements = DetteServiceFacade::getPaiements($id);
        return compact('paiements');
    }

/**
     * @OA\Post(
     *     path="/v1/dettes/{id}/payer",
     *     tags={"Dette"},
     *     summary="Make a payment for a dette",
     *     description="Pay a portion or full amount of a dette",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the dette",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePaiementRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment successful",
     *         @OA\JsonContent(ref="#/components/schemas/Dette")
     *     ),
     *     @OA\Response(response=400, description="Invalid payment data")
     * )
     */
    public function payer(StorePaiementRequest $request, $id)
    {
        $montant = $request->input('montant');
        $dette = DetteServiceFacade::payer($id, $montant);
        return compact('dette');
    }
}
