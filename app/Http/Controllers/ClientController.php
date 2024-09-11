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

class ClientController extends Controller
{
    use RestResponseTrait;

    public function __construct() {
        $this->authorizeResource(Client::class, 'client');
    }
    /**
     * Display a listing of the resource.
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

    public function getDettes(string $id, User $user)
    {
        $client = ClientServiceFacade::dettes($id);
        return compact('client');
    }

    public function getClientWithDebtswithArticle($id){
        $client = ClientServiceFacade::getClientWithDebtswithArticle($id);
        return compact('client');
    }
}
