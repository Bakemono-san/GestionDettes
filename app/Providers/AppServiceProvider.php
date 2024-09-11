<?php

namespace App\Providers;

use App\Contracts\ArticleRepositoryImpl;
use App\Contracts\ArticleServiceInt;
use App\Contracts\ClientRepositoryInt;
use App\Contracts\ClientServiceInt;
use App\Contracts\DetteRepositoryInt;
use App\Contracts\DetteServiceInt;
use App\Contracts\InfoBipServiceInt;
use App\Contracts\PaiementRepositoryInt;
use App\Contracts\PaiementServiceInt;
use App\Contracts\QrCodeServiceInt;
use App\Contracts\UploadImageServiceInt;
use App\Contracts\UserRepositoryInt;
use App\Contracts\UserServiceInt;
use App\Http\Middleware\FormatResponse;
use App\Mail\SendMail;
use App\Repositories\ArticleRepository;
use App\Repositories\ClientRepository;
use App\Repositories\DetteRepository;
use App\Repositories\PaiementRepository;
use App\Repositories\UserRepository;
use App\Services\ArticleService;
use App\Services\ClientService;
use App\Services\DetteService;
use App\Services\FirebaseService;
use App\Services\InfoBipService;
use App\Services\PaiementService;
use App\Services\PdfService;
use App\Services\QrCodeService;
use App\Services\UploadImageService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ArticleServiceInt::class,ArticleService::class);
        $this->app->singleton(ArticleRepositoryImpl::class,ArticleRepository::class);
        $this->app->singleton(ClientRepositoryInt::class,ClientRepository::class);
        $this->app->bind(ClientServiceInt::class,ClientService::class);
        $this->app->singleton(UploadImageServiceInt::class, UploadImageService::class);
        $this->app->singleton(QrCodeServiceInt::class, QrCodeService::class);
        $this->app->singleton('PdfService', PdfService::class);
        $this->app->singleton(UserRepositoryInt::class,UserRepository::class);
        $this->app->singleton(UserServiceInt::class,UserService::class);
        $this->app->singleton(DetteRepositoryInt::class,DetteRepository::class);
        $this->app->singleton(DetteServiceInt::class,DetteService::class);
        $this->app->singleton(PaiementRepositoryInt::class,PaiementRepository::class);
        $this->app->singleton(PaiementServiceInt::class,PaiementService::class);
        $this->app->singleton('firebase',FirebaseService::class);
        $this->app->singleton(InfoBipServiceInt::class,InfoBipService::class);
        $this->app->singleton(SendMail::class, function ($app) {
            // Ensure that parameters are available here
            return new SendMail($app['userData'], $app['pdf']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
    }
}
