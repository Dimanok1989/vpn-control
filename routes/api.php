<?php

use App\Http\Controllers\IpsecApiController;
use App\Http\Middleware\AccessTokenMiddleware;
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

Route::middleware(AccessTokenMiddleware::class)->group(function () {
    Route::get('ipsec/status', [IpsecApiController::class, 'status']);
});