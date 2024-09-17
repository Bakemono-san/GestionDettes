<?php

namespace App\Http\Controllers;

use App\Facades\ClientRepositoryFacade;
use App\Facades\DemandeServiceFacade;
use App\Facades\UserRepositoryFacade;
use App\Http\Requests\StoreDemandeRequest;
use App\Models\Demandes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class DemandesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Demandes::class);
        if($request->query->has('etat')) {
            $query->where('etat',$request->query('etat'));
        }
        // $query = Demandes::where('etat',$request->query('etat'));


        $demandes = $query->get();

        return compact('demandes');
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
    public function store(StoreDemandeRequest $request)
    {
        $user = Auth::user();

        $client = ClientRepositoryFacade::getByUserId(3);

        $request['client_id'] = $client->id;
        $demandes = DemandeServiceFacade::createDemande($request);
        return compact('demandes');
    }

    /**
     * Display the specified resource.
     */
    public function show(Demandes $demandes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Demandes $demandes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Demandes $demandes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Demandes $demandes)
    {
        //
    }

    public function getMyDemandes(){
        $user = Auth::user();
        $client = ClientRepositoryFacade::getByUserId($user->id);
        $demandes = Demandes::where('client_id',$client->id)->where('etat','En attente')->get() ?? null;
        return compact('demandes');
    }

    public function getNotifications(){
        $user = Auth::user();
        $userTest = UserRepositoryFacade::find(3);
        $notifications = $userTest->notifications;

        return compact('notifications');
    }

    public function relance($id){
        $demandes = Demandes::find($id);

        $demande = DemandeServiceFacade::relance($demandes);
        return compact('demande');
    }

    public function getNotificationsDemandes(){
        $user = Auth::user();
        $userTest = UserRepositoryFacade::find(3);
        $notifications = $userTest->unreadNotifications;
        $demandes = [];

        foreach($notifications as $notification){
            if(strpos($notification->data["message"],'demande')){
                $demandes[] = $notification;
            }
        }

        return compact('demandes');
    }

    public function getNotificationsResponse(){
        $user = Auth::user();   
        $userTest = UserRepositoryFacade::find(3);
        $notifications = $userTest->unreadNotifications;
        $reponses = [];

        foreach($notifications as $notification){
            if($notification->data["type"] == 'reponse'){
                $reponses[] = $notification;
            }
        }
        return compact('reponses');
    }

    public function getDemandesArticles($idDemande){
        $articlesStats = DemandeServiceFacade::getDemandeService($idDemande);

        return compact('articlesStats');
    }

    public function traiterDemande(Request $request, $id){
        $demandes = DemandeServiceFacade::traiterDemande($request, $id);

        return compact('demandes');
        
    }
}
