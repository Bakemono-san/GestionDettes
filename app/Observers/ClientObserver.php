<?php

namespace App\Observers;

use App\Events\asyncTransfertProcess;
use App\Facades\UserRepositoryFacade;
use App\Jobs\CreateQrCode;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientObserver
{
    /**
     * Handle the Client "created" event.
     *
     * @param \App\Models\Client $client
     * @return void
     */
    public function created(Client $client)
    {
        CreateQrCode::dispatch($client);
        $request = request();

        $userData = $request->has('user') ? $request->user : null;
        if($userData){

            $user = UserRepositoryFacade::create($userData);
            
            $photo = $request->file('user.photo');
            $photoContents = file_get_contents($photo->getRealPath());
            $photoBase64 = 'data:image/png;base64,' . base64_encode($photoContents);
            $filepath = $photo->storeAs('photos', $client->surname . '.' . $photo->getClientOriginalExtension(), 'public');
            
            event(new asyncTransfertProcess($client, $photoBase64, $user, $filepath));
        }
    }
}
