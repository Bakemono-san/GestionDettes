<?php
// app/Providers/CustomAuthServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\AuthenticationServiceInterface;
use App\Services\PassportAuthService;
use App\Services\SanctumAuthService;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;

class CustomAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AuthenticationServiceInterface::class, function ($app) {
            // You can set the authentication driver in your .env file or config
            $authDriver = env('AUTH_DRIVER', 'passport'); // Default to Sanctum

            if ($authDriver === 'sanctum') {
                return new SanctumAuthService();
            }

            return new PassportAuthService();
        });
    }

     /**
     * Bootstrap any authentication services.
     *
     * @return void
     */
    public function boot(){
        //
    }
}
