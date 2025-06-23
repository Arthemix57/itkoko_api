<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PubliciteController;
use App\Http\Controllers\ProduitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// pour les requetes commune non authentifie
Broadcast::routes(['middleware' => ['auth:api']]);
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('send/mail', 'sendmail');
    Route::post('send/code', 'send');
    Route::post('valitated', 'valitated');
    Route::post('resetpassword/{id}', 'resetpassword');
});

Route::middleware('auth:api')->group(function () {
    // pour les requetes commune
    Route::controller(AuthController::class)->group(function () {
        Route::get('/logout', 'logout');
        Route::get('/user', 'user');
        Route::put('editUser/{id}', 'editUser');
        Route::delete('/deleteUser/{id}', 'deleteUser');
    });

    Route::controller(PubliciteController::class)->group(function () {
        Route::get('publicites', 'index')->middleware('admin'); // Publicities for admin
        Route::get('publicite/users', 'indexforusers'); // Publicities for users
        Route::get('publicite/show/{id}', 'show'); // Show specific publicity
        Route::post('publicites', 'store')->middleware('admin'); // Create a new publicity
        Route::put('publicite/update/{id}', 'update')->middleware('admin'); // Update a specific publicity
        Route::delete('publicite/delete/{id}', 'destroy')->middleware('admin'); // Delete a specific publicity
      
    // pour les requetes produits
    Route::controller(ProduitController::class)->group(function () {
        Route::get('/produits', 'index'); // Get all products
        Route::get('/produits/users', 'indexforusers')->middleware('users'); // Get all products for users
        Route::get('/produits/{id}', 'show'); // Get a specific product by ID
        Route::post('/produits', 'store')->middleware('amin'); // Create a new product
        Route::put('/produits/{id}', 'update'); // Update a specific product by ID
        Route::delete('/produits/{id}', 'destroy')->middleware('admin'); // Delete a specific product by ID
    });
});   
});
