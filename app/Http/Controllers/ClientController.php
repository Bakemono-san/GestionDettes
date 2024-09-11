<?php

namespace App\Http\Controllers;

use App\Enums\StateEnum;
use App\Facades\ClientServiceFacade;
use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Dette;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="Clients",
 *     description="Gestion des clients"
 * )
 */
class ClientController extends Controller
{
    use RestResponseTrait;

    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/clients",
     *     summary="Liste tous les clients",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         description="Filtres pour la recherche des clients",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(ref="#/components/schemas/ClientCollection")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur dans la requête"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     )
     * )
     */
    public function index(Request $request, User $user)
    {
        // $this->authorize('viewAny', $user);
        $filters = $request->all();
        $clients = ClientServiceFacade::all($filters);
        $message = $clients->count() . ' client(s) trouvé(s)';
        $data = $clients->count() > 0 ? new ClientCollection($clients) : [];
        return $this->sendResponse($data, StateEnum::SUCCESS, $message, 200);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/clients",
     *     summary="Création d'un nouveau client",
     *     tags={"Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreClientRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur dans la requête"
     *     )
     * )
     */
    public function store(StoreClientRequest $request, User $user)
    {
        // $clientData = $request->only('surname', 'adress', 'telephone');
        // $photo = $request->file('user.photo');

        // $userData = $request->has('user') ? $request->user : null;
        $client = ClientServiceFacade::create($request);
        return compact('client');
        // return $this->sendResponse($client, StateEnum::SUCCESS, 'client créé avec success', 201);
    }


    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/clients/{id}",
     *     summary="Afficher un client spécifique",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identifiant du client",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client retrouvé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/ClientResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */
    public function show(string $id, User $user)
    {
        $this->authorize('view', $user);
        $client = Client::find($id);

        if (!$client) {
            return $this->sendResponse([], StateEnum::ECHEC, 'aucun client avec cet identifiant ', 400);
        }

        return $this->sendResponse(new ClientResource($client), StateEnum::SUCCESS, 'client retrouve avec success', 200);
    }


    public function get(string $id, User $user)
    {
        $clients = ClientServiceFacade::get($id);
        return compact('clients');
    }

    /**
     * @OA\Get(
     *     path="/clients/{id}/dettes",
     *     summary="Obtenir les dettes d'un client",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identifiant du client",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dettes retrouvées",
     *         @OA\JsonContent(ref="#/components/schemas/DetteCollection")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */
    public function getDettes(string $id, User $user)
    {
        $client = ClientServiceFacade::dettes($id);
        return compact('client');
    }

    /**
     * @OA\Get(
     *     path="/clients/{id}/debts-with-article",
     *     summary="Obtenir les dettes et articles d'un client",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identifiant du client",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client avec dettes et articles retrouvés",
     *         @OA\JsonContent(ref="#/components/schemas/ClientWithDebtsArticles")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     )
     * )
     */
    public function getClientWithDebtswithArticle($id)
    {
        $client = ClientServiceFacade::getClientWithDebtswithArticle($id);
        return compact('client');
    }
}
