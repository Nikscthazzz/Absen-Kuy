<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [ApiController::class, 'login']);
Route::get('/getdatauser/{user}', [ApiController::class, 'getDataUser']);

Route::post("/ubahalamat", [ApiController::class, "ubahAlamat"]);
Route::post("/ubahnotelp", [ApiController::class, "ubahNoTelp"]);

Route::post("/checkin", [ApiController::class, "checkin"]);
Route::post("/checkout", [ApiController::class, "checkout"]);
