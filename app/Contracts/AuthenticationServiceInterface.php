<?php
// app/Contracts/AuthenticationServiceInterface.php
namespace App\Contracts;

interface AuthenticationServiceInterface
{
    public function login(array $data);
}
