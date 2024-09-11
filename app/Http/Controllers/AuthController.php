<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('login','password'))) {
            $user = Auth::user();
            $token = $user->createToken('LaravelPassportToken')->accessToken;

            // $refreshToken = $this->createRefreshToken($user);

            return response()->json(['token' => $token,'refresh_token' => "hello",'user' => new UserResource($user)], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
