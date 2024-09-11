<?php
// app/Services/PassportAuthService.php
namespace App\Services;

use App\Contracts\AuthenticationServiceInterface;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;

class PassportAuthService implements AuthenticationServiceInterface
{

    public function login(array $request)
    {

        if (Auth::attempt($request)) {
            $user = User::find(Auth::user()->id);
            $token = $user->createToken('LaravelPassportToken')->accessToken;

            return response()->json(['token' => $token,'user' =>$user], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
