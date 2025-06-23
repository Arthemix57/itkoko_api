<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PubliciteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

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
    Route::post('login/serveur', 'loginServeur');
    Route::post('login/admin', 'loginAdmin');
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
        Route::get('publicites', 'indexforusers'); // Publicities for users
        Route::get('publicite/show/{id}', 'show'); // Show specific publicity
        Route::post('publicites', 'store')->middleware('admin'); // Create a new publicity
        Route::put('publicite/update/{id}', 'update')->middleware('admin'); // Update a specific publicity
        Route::delete('publicite/delete/{id}', 'destroy')->middleware('admin'); // Delete a specific publicity
    });
});