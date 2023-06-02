<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EntreeMedicamentController;
use App\Http\Controllers\SortieMedicamentController;
use App\Http\Controllers\UserController;
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

Route::post('/login', [AuthController::class, 'login']);

Route::apiResources([
    "entrees" => EntreeMedicamentController::class,
    "sorties" => SortieMedicamentController::class,
    "users" => UserController::class,
]);

Route::prefix("entrees")->group(function() {
    Route::post("/trie", [EntreeMedicamentController::class, "setTrie"]);
    Route::post("/filtre", [EntreeMedicamentController::class, "setFiltre"]);
});

Route::prefix("sorties")->group(function() {
    Route::post("/trie", [SortieMedicamentController::class, "setTrie"]);
    Route::post("/filtre", [SortieMedicamentController::class, "setFiltre"]);
});