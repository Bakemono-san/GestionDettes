<?php

namespace App\Observers;

use App\Jobs\UploadPhoto;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        $request = request();

        if($request->has('photo')){
        $photo = $request->file('photo');
        } else {
            $photo = $request->file('user.photo');
        }

        $filepath = $photo->storeAs('photos', $photo->getClientOriginalExtension(), 'public');
        UploadPhoto::dispatch($user, $filepath);
    }
}
