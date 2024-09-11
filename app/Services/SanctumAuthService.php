<?php

namespace App\Services;

use App\Contracts\AuthenticationServiceInterface;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Http\Request;

class SanctumAuthService implements AuthenticationServiceInterface
{
    public function login(array $request)
    {
        // Attempt to authenticate the user using the provided credentials
        if (Auth::attempt($request)) {
            // Retrieve the authenticated user
            $user = User::find(Auth::user()->id);
            
            // Create a new Sanctum access token for the user
            $accessToken = $user->createToken('LaravelSanctumToken')->plainTextToken;

            // Generate a refresh token
            $refreshToken = Str::random(64);

            // Store the refresh token (in a cache or database)
            Cache::put('refresh_token_' . $user->id, $refreshToken, now()->addDays(30)); // Store for 30 days

            // Return the access token, refresh token, and user data in the response
            return response()->json([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'user' => new UserResource($user)
            ], 200);
        } else {
            // Return an unauthorized error if authentication fails
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function refresh(Request $request)
    {
        // Validate the provided refresh token
        $refreshToken = $request->input('refresh_token');
        $userId = $request->input('user_id');

        // Retrieve the stored refresh token from cache
        $storedRefreshToken = Cache::get('refresh_token_' . $userId);

        // Check if the refresh token matches the stored one
        if ($storedRefreshToken && hash_equals($storedRefreshToken, $refreshToken)) {
            // Invalidate the old refresh token
            Cache::forget('refresh_token_' . $userId);

            // Generate new tokens
            $user = User::find($userId);
            $newAccessToken = $user->createToken('LaravelSanctumToken')->plainTextToken;
            $newRefreshToken = Str::random(64);

            // Store the new refresh token
            Cache::put('refresh_token_' . $user->id, $newRefreshToken, now()->addDays(30)); // Store for 30 days

            // Return the new tokens
            return response()->json([
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
            ], 200);
        } else {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }
    }
}
