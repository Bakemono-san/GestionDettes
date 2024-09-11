<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Contracts\AuthenticationServiceInterface;
use Illuminate\Http\Request;

class AuthController2 extends Controller
{
    protected $authService;

    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');

       $user =  $this->authService->login($credentials);
       return compact('user');
    }
}

