<?php

use Illuminate\Http\Request;
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

Route::post('/register', [\App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\API\AuthController::class, 'login'])->name('login');

// Gifs
Route::get('/gifs', [\App\Http\Controllers\API\GifController::class, 'search'])->middleware('auth:api');
Route::get('/gifs/{id}', [\App\Http\Controllers\API\GifController::class, 'searchById'])->middleware('auth:api');
Route::post('/gifs', [\App\Http\Controllers\API\GifController::class, 'store'])->middleware('auth:api');
