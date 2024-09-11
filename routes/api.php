<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthController2;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\userController;
use App\Http\Middleware\FormatResponse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    Mail::raw('This is a test email from Laravel.', function ($message) {
        $message->to('fallmakh026@gmail.com')
                ->subject('Test Email');
    });

    return 'Email sent!';
});


Route::get('/firebase', [FirebaseController::class, 'index']);
Route::post('/firebase', [FirebaseController::class, 'store']);

// Route::middleware('auth:api')->post('/login', [AuthController::class, 'login']);

Route::post('login', [AuthController2::class, 'login'])->middleware(FormatResponse::class)->name('login');
Route::apiResource('/users', userController::class)->only(['index', 'store','show']);

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    
    Route::post('/role', [RoleController::class, 'store']);
    
    
    Route::get('/client/{id}', [ClientController::class, 'getClientWithDebtswithArticle'])->name('clients.getClientWithDebtswithArticle');
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store','show']);
    Route::get('/clients/{id}/user', [ClientController::class, 'get'])->name('articlesWithUser');
    Route::get('/clients/{id}/dettes', [ClientController::class, 'getDettes']);
    Route::post('/register', [userController::class,'createUserForClient']);

    Route::prefix('dettes')->group(function(){
        Route::post('/',[DetteController::class,'store'])->name('dette.create');
        Route::get('/',[DetteController::class,'index'])->name('dette.all');
        Route::get('/{id}',[DetteController::class,'show'])->name('dette.show');
        Route::get('/{id}/articles',[DetteController::class,'getArticles'])->name('dette.articles');
        Route::get('/{id}/paiements',[DetteController::class,'getPaiements'])->name('dette.paiements');
        Route::post('/{id}/payer', [DetteController::class, 'payer'])->name('dette.payer');
    });
    

    Route::prefix('articles')->group(function () {
        // GET /articles - Display a listing of the articles
        Route::get('/', [ArticlesController::class, 'index'])->name('articles.index');
    
        // POST /articles - Store a newly created article
        Route::post('/', [ArticlesController::class, 'store'])->name('articles.store');
    
        // GET /articles/{id} - Display a specific article
        Route::get('/{id}', [ArticlesController::class, 'show'])->name('articles.show');

        Route::post('/libelle', [ArticlesController::class, 'get'])->name('articles.libelle');
    
        // PUT /articles/{id} - Update the specified article
        Route::put('/{id}', [ArticlesController::class, 'update'])->name('articles.update');
    
        // PATCH /articles/mass-update - Mass update articles
        Route::post('/stock', [ArticlesController::class, 'massUpdate'])->name('articles.massUpdate');
    
        // DELETE /articles/{id} - Remove the specified article
        Route::delete('/{id}', [ArticlesController::class, 'destroy'])->name('articles.destroy');
    });
});

// Route group for articles management
